<?php
/**
 * Tests that expLayoutsSiteInstaller creates the folders it copies the binary files into within the limit for new
 * directories the kernel sets (EZP_DIR_MODE_MAX in config.php, see eZDir::dirMode()), and exactly as a plain
 * mkdir( 0777 ) without a limit or on a kernel without that helper.
 *
 * Run directly with:
 *   php tests/explayoutssiteinstallerfilemodetest.php            no limit: the modes of a plain mkdir( 0777 )
 *   php tests/explayoutssiteinstallerfilemodetest.php limit      EZP_DIR_MODE_MAX=0770, EZP_FILE_MODE_MAX=0660
 *   php tests/explayoutssiteinstallerfilemodetest.php nokernel   without the kernel helpers
 *
 * The kernel helpers are loaded from the installation the extension is in (../../../lib/ezfile). The sandbox is made
 * under EXPLAYOUTS_TEST_TMP, or the system temporary directory, and removed afterwards.
 */

$mode = isset( $argv[1] ) ? $argv[1] : 'nolimit';
if ( !in_array( $mode, array( 'nolimit', 'limit', 'nokernel' ), true ) )
{
    echo "usage: php {$argv[0]} [nolimit|limit|nokernel]\n";
    exit( 2 );
}
$lib = __DIR__ . '/../../../lib/ezfile/classes';
if ( $mode !== 'nokernel' )
{
    if ( !is_file( $lib . '/ezdir.php' ) )
    {
        echo "SKIP: no kernel next to the extension ($lib)\n";
        exit( 0 );
    }
    require_once $lib . '/ezfile.php';
    require_once $lib . '/ezdir.php';
    if ( !method_exists( 'eZDir', 'dirMode' ) )
    {
        echo "SKIP: the kernel has no eZDir::dirMode() (before 6.0.15)\n";
        exit( 0 );
    }
}
if ( $mode === 'limit' )
{
    define( 'EZP_DIR_MODE_MAX', 0770 );
    define( 'EZP_FILE_MODE_MAX', 0660 );
    // no umask in the way: the cap of the limit alone is seen (a plain mkdir( 0777 ) gives 0777, the copy 0770)
    umask( 0 );
}
require_once __DIR__ . '/../classes/explayoutssiteinstaller.php';

$base = getenv( 'EXPLAYOUTS_TEST_TMP' ) ?: sys_get_temp_dir();
$sandbox = rtrim( $base, '/' ) . '/explayouts-filemode-test-' . getmypid();
if ( !@mkdir( $sandbox . '/src/sub', 0700, true ) || !@mkdir( $sandbox . '/plain/sub', 0777, true ) )
{
    echo "FAIL: cannot create the sandbox $sandbox\n";
    exit( 1 );
}
file_put_contents( $sandbox . '/src/sub/file.txt', 'x' );

$installer = ( new ReflectionClass( 'expLayoutsSiteInstaller' ) )->newInstanceWithoutConstructor();
$copy = new ReflectionMethod( 'expLayoutsSiteInstaller', 'recursiveCopy' );
if ( PHP_VERSION_ID < 80100 )
    $copy->setAccessible( true );
$copy->invoke( $installer, $sandbox . '/src', $sandbox . '/copy' );

$failures = 0;
clearstatcache();
foreach ( array( '', '/sub' ) as $dir )
{
    $plain = fileperms( $sandbox . '/plain' . $dir ) & 07777;
    $made = fileperms( $sandbox . '/copy' . $dir ) & 07777;
    $ok = $made === ( $mode === 'limit' ? ( $plain & 0770 ) : $plain );
    printf( "%s copy%s: %04o (plain mkdir 0777: %04o)\n", $ok ? 'PASS' : 'FAIL', $dir === '' ? '' : $dir, $made, $plain );
    if ( !$ok )
        ++$failures;
}
if ( !is_file( $sandbox . '/copy/sub/file.txt' ) )
{
    echo "FAIL: the file was not copied\n";
    ++$failures;
}

$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $sandbox, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
foreach ( $it as $f )
    $f->isDir() ? rmdir( $f->getPathname() ) : unlink( $f->getPathname() );
rmdir( $sandbox );

echo $failures ? "FAIL ($mode)\n" : "PASS ($mode)\n";
exit( $failures ? 1 : 0 );
