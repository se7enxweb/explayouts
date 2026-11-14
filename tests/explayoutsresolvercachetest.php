<?php
/**
 * Tests for the layout resolver's cache in a process that serves many requests (Exponential Velocity with
 * ForkPerRequest=disabled, or a server that keeps class statics between requests): what the process remembers is
 * checked against the cache file once per request, so a clear, a rewrite or an expiry made anywhere is seen by the
 * next request; the TTL is honored for remembered entries too; and a clear (which a publish and a rule change run)
 * bumps the admin generation and forgets everything.
 *
 * Needs the kernel (eZINI, eZFile); no database. The cache goes to a directory of its own under var/tmp. Run from
 * the root of an installation with explayouts in extension/explayouts:
 *   php extension/explayouts/tests/explayoutsresolvercachetest.php
 */

require 'autoload.php';
require_once __DIR__ . '/../classes/explayoutsresolver.php';

$failures = 0;
function check( $condition, $message )
{
    global $failures;
    if ( !$condition )
    {
        echo "FAIL: $message\n";
        $failures++;
    }
}

function call( $name, ...$args )
{
    $method = new ReflectionMethod( 'expLayoutsResolver', $name );
    // PHP 8.0 needs it for a non-public member, 8.1+ does not (and 8.5 deprecates it)
    if ( PHP_VERSION_ID < 80100 )
        $method->setAccessible( true );
    return $method->invoke( null, ...$args );
}

function staticProperty( $name )
{
    $property = new ReflectionProperty( 'expLayoutsResolver', $name );
    if ( PHP_VERSION_ID < 80100 )
        $property->setAccessible( true );
    return $property;
}

function getStatic( $name )
{
    return staticProperty( $name )->getValue( null );
}

function setStatic( $name, $value )
{
    staticProperty( $name )->setValue( null, $value );
}

/** A new request of the same process */
function nextRequest()
{
    static $time = 5000.0;
    $time += 1.25;
    $_SERVER['REQUEST_TIME_FLOAT'] = $time;
}

/** The file of a path's entry */
function entryFile( $path, $siteAccess )
{
    return call( 'cacheFile', call( 'cacheKey', $path, $siteAccess ) );
}

/** Another process writes the entry the way the resolver does (a new file renamed over the old one) */
function writtenElsewhere( $path, $siteAccess, $layoutId, $expires )
{
    $file = entryFile( $path, $siteAccess );
    $data = array( 'path' => $path, 'siteaccess' => $siteAccess, 'rule_id' => 3, 'layout_id' => $layoutId, 'expires' => $expires );
    eZFile::create( basename( $file ), dirname( $file ), '<?php return ' . var_export( $data, true ) . ';', true );
}

function layoutOf( $cached )
{
    return is_array( $cached ) ? (int)$cached['layout_id'] : false;
}

$requestTime = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? $_SERVER['REQUEST_TIME_FLOAT'] : null;

// a cache directory of the test's own
$varDir = 'var/tmp/explayouts-resolver-cache-test-' . getmypid();
$siteIni = eZINI::instance( 'site.ini' );
$oldVarDir = $siteIni->variable( 'FileSettings', 'VarDir' );
$siteIni->setVariable( 'FileSettings', 'VarDir', $varDir );
foreach ( array( 'resolvedCache', 'resolvedStamps', 'resolvedChecked', 'adminMemo' ) as $name )
    setStatic( $name, array() );
setStatic( 'cacheRequest', null );

// 1. a persistent worker: two requests with a clear in between
nextRequest();
call( 'writeCache', 'about', 'site', 5, 7 );
check( layoutOf( call( 'readCache', 'about', 'site' ) ) === 7, 'a written entry is read back in the same request' );
nextRequest();
check( layoutOf( call( 'readCache', 'about', 'site' ) ) === 7, 'and in the next request while its file is unchanged' );
check( is_file( entryFile( 'about', 'site' ) ), 'the entry is on disk' );

unlink( entryFile( 'about', 'site' ) ); // another process clears the cache
check( layoutOf( call( 'readCache', 'about', 'site' ) ) === 7, 'the request that already checked the entry keeps it' );
nextRequest();
check( call( 'readCache', 'about', 'site' ) === false, 'the next request misses an entry whose file was deleted elsewhere' );
check( !isset( getStatic( 'resolvedCache' )[call( 'cacheKey', 'about', 'site' )] ), 'and forgets it' );

// a rewrite by another process (a rule changed, resolved again by Apache)
nextRequest();
call( 'writeCache', 'news', 'site', 5, 7 );
nextRequest();
check( layoutOf( call( 'readCache', 'news', 'site' ) ) === 7, 'the entry before the rewrite' );
writtenElsewhere( 'news', 'site', 9, time() + 600 );
nextRequest();
check( layoutOf( call( 'readCache', 'news', 'site' ) ) === 9, 'the next request reads the rewritten entry, not the remembered one' );

// one stat per entry and request, none for a second lookup in the same request
nextRequest();
call( 'readCache', 'news', 'site' );
check( count( getStatic( 'resolvedChecked' ) ) === 1, 'a new request starts over: only the entry looked up is checked' );

// without REQUEST_TIME_FLOAT (one process per request, the command line) the entry is checked once per process
unset( $_SERVER['REQUEST_TIME_FLOAT'] );
check( layoutOf( call( 'readCache', 'news', 'site' ) ) === 9, 'no request time: the entry is read' );

// 2. the TTL
nextRequest();
writtenElsewhere( 'old', 'site', 4, time() - 1 );
check( call( 'readCache', 'old', 'site' ) === false, 'an expired file entry is a miss' );
check( !is_file( entryFile( 'old', 'site' ) ), 'and its file is removed' );

call( 'writeCache', 'ttl', 'site', 5, 6 );
$key = call( 'cacheKey', 'ttl', 'site' );
$memory = getStatic( 'resolvedCache' );
$memory[$key]['expires'] = time() - 1; // the entry has lived out its TTL in this process
setStatic( 'resolvedCache', $memory );
writtenElsewhere( 'ttl', 'site', 6, time() - 1 );
check( call( 'readCache', 'ttl', 'site' ) === false, 'a remembered entry past its TTL is a miss, in the same request too' );
check( !isset( getStatic( 'resolvedCache' )[$key] ), 'and is forgotten' );

// 3. a publish or a rule change clears the cache: generation bumped, nothing remembered, files gone
nextRequest();
call( 'writeCache', 'shop', 'site', 5, 8 );
$generation = expLayoutsResolver::adminGeneration();
expLayoutsResolver::clearCache();
check( expLayoutsResolver::adminGeneration() !== $generation, 'a clear bumps the admin generation' );
check( getStatic( 'resolvedCache' ) === array() && getStatic( 'resolvedStamps' ) === array(), 'a clear forgets every remembered entry' );
check( call( 'readCache', 'shop', 'site' ) === false, 'the next lookup after a clear misses, in the same request too' );
check( glob( $varDir . '/cache/explayouts/resolver/*.php' ) === array(), 'a clear removes every entry file' );

// clean up
foreach ( glob( $varDir . '/cache/explayouts/resolver/*' ) ?: array() as $file )
    unlink( $file );
@rmdir( $varDir . '/cache/explayouts/resolver' );
@unlink( $varDir . '/cache/explayouts/admin_generation' );
@rmdir( $varDir . '/cache/explayouts' );
@rmdir( $varDir . '/cache' );
@rmdir( $varDir );
$siteIni->setVariable( 'FileSettings', 'VarDir', $oldVarDir );
foreach ( array( 'resolvedCache', 'resolvedStamps', 'resolvedChecked', 'adminMemo' ) as $name )
    setStatic( $name, array() );
setStatic( 'cacheRequest', null );
if ( $requestTime === null )
    unset( $_SERVER['REQUEST_TIME_FLOAT'] );
else
    $_SERVER['REQUEST_TIME_FLOAT'] = $requestTime;

if ( $failures > 0 )
{
    echo "$failures check(s) failed\n";
    exit( 1 );
}
echo "OK\n";
