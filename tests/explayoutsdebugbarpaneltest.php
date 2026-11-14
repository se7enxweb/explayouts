<?php
/**
 * Tests for expLayoutsDebugBarPanel, the Exp Layouts panel of the Exp Debug bar: what it shows for the layout the
 * resolver and the renderer handled for the page in this request (not for other nodes the page resolves), the links
 * into the administration, the nesting of blocks, the escaping of names, and that it stays hidden where the page's
 * layout was not looked up.
 *
 * Needs the kernel (eZINI, ezpI18n, expDebugBarPanelInterface from Exponential 6.0.15); no database. Run from the root
 * of an installation with explayouts in extension/explayouts:
 *   php extension/explayouts/tests/explayoutsdebugbarpaneltest.php
 */

require 'autoload.php';
if ( !interface_exists( 'expDebugBarPanelInterface' ) )
{
    echo "SKIP: the kernel has no expDebugBarPanelInterface (Exponential 6.0.15 and later)\n";
    exit( 0 );
}
require_once __DIR__ . '/../classes/explayoutsresolver.php';
require_once __DIR__ . '/../classes/explayoutsrenderer.php';
require_once __DIR__ . '/../classes/explayoutsdebugbarpanel.php';

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

function setStatic( $class, $name, $value )
{
    $property = new ReflectionProperty( $class, $name );
    // PHP 8.0 needs it for a non-public member, 8.1+ does not (and 8.5 deprecates it)
    if ( PHP_VERSION_ID < 80100 )
        $property->setAccessible( true );
    $property->setValue( null, $value );
}

/** The resolver's answer for the page: on the command line the request path is "home" */
function pageResolved( $source, $ruleId, $layoutId )
{
    // a new panel for every state: it looks the page's resolution up once
    global $panel;
    $panel = new expLayoutsDebugBarPanel();
    setStatic( 'expLayoutsResolver', 'resolutions', array( 'home' => array( 'path' => 'home', 'siteaccess' => 'site', 'source' => $source,
                                                                            'rule_id' => $ruleId, 'layout_id' => $layoutId ) ) );
}

/** A prepared layout as expLayoutsRenderer::prepareLayout() returns it, with a nested block and markup in a name */
function preparedLayout()
{
    $block = function ( $id, $kind, $name, $parent = 0, $children = array() )
    {
        return array( 'id' => $id, 'definition_identifier' => $kind, 'view_type' => 'default', 'name' => $name,
                      'parent_id' => $parent, 'children' => $children );
    };
    $child = $block( 3, 'title', 'Hello <b>world</b>', 2 );
    return array(
        'id' => 27, 'identifier' => 'landing', 'name' => 'Landing <Page>', 'layout_type' => 'layout_2', 'block_count' => 3,
        'zones' => array(
            array( 'id' => 1, 'identifier' => 'header', 'linked_layout_id' => 2, 'linked_zone_identifier' => 'header',
                   'blocks' => array( $block( 1, 'tpl_block', '' ) ) ),
            array( 'id' => 2, 'identifier' => 'main', 'linked_layout_id' => null, 'linked_zone_identifier' => null,
                   'blocks' => array( $block( 2, 'column', 'Column', 0, array( $child ) ), $child ) ),
            array( 'id' => 3, 'identifier' => 'pre_footer', 'linked_layout_id' => null, 'linked_zone_identifier' => null, 'blocks' => array() ),
            // a block whose parent is not in the zone: drawn at the top level
            array( 'id' => 4, 'identifier' => 'aside', 'linked_layout_id' => null, 'linked_zone_identifier' => null,
                   'blocks' => array( $block( 9, 'text', 'Orphan', 777 ) ) ),
        ),
    );
}

$panel = new expLayoutsDebugBarPanel();

// nothing resolved or rendered: hidden
setStatic( 'expLayoutsResolver', 'resolutions', array() );
setStatic( 'expLayoutsRenderer', 'prepared', array() );
check( $panel->visible() === false, 'hidden when no layout was looked up on the page' );

// only another node's layout was resolved (a menu): not the page's
setStatic( 'expLayoutsResolver', 'resolutions', array( 'other/node' => array( 'path' => 'other/node', 'siteaccess' => 'site', 'source' => 'rule', 'rule_id' => 5, 'layout_id' => 8 ) ) );
check( expLayoutsResolver::pageResolution() === null, 'the resolution of another path is not the page\'s' );
check( $panel->visible() === false, 'hidden when only other nodes were resolved' );

// a rendered layout chosen by a rule, next to another prepared layout (a linked one)
pageResolved( 'rule', 17, 27 );
setStatic( 'expLayoutsRenderer', 'prepared', array( 2 => array( 'id' => 2, 'identifier' => 'header_footer', 'name' => 'Header / Footer',
                                                               'layout_type' => 'layout_1', 'zones' => array() ),
                                                    27 => preparedLayout() ) );
$panel = new expLayoutsDebugBarPanel();

// the command line runs as the anonymous user, who may not edit layouts
$anonymous = eZUser::currentUser()->hasAccessTo( 'explayouts_ui', 'edit' );
if ( $anonymous['accessWord'] === 'no' )
    check( $panel->visible() === false, 'hidden from a user who may not edit layouts' );
else
    echo "NOTE: the current user may edit layouts here; the hidden case for others is not checked\n";

$chip = $panel->chip();
check( $chip['text'] === 'Layout: Landing <Page>', 'the chip names the layout (the bar escapes it): ' . $chip['text'] );
check( $chip['title'] === 'Rule 17 - 4 zones, 4 blocks', 'the chip title: ' . $chip['title'] );
check( $chip['level'] === 'ok', 'the chip level' );

$html = $panel->html();
check( strpos( $html, 'Landing &lt;Page&gt;' ) !== false && strpos( $html, '<Page>' ) === false, 'the layout name is escaped' );
check( strpos( $html, 'Hello &lt;b&gt;world&lt;/b&gt;' ) !== false && strpos( $html, '<b>world' ) === false, 'a block name is escaped' );
check( strpos( $html, '/explayouts_ui/layout_edit/27"' ) !== false, 'link to edit the layout' );
check( strpos( $html, '/explayouts_ui/rule_edit/17"' ) !== false, 'link to edit the rule' );
check( preg_match( '#&\#8627; Hello#', $html ) === 1, 'a nested block is marked as such' );
check( substr_count( $html, 'Hello &lt;b&gt;' ) === 1, 'a nested block is listed once, under its parent' );
// block_edit asks for explayouts/edit: the block links only for those who have it
$blocks = eZUser::currentUser()->hasAccessTo( 'explayouts', 'edit' );
if ( $blocks['accessWord'] === 'no' )
    check( strpos( $html, '/explayouts_ui/block_edit/' ) === false, 'no block links without explayouts/edit' );
else
    check( strpos( $html, '/explayouts_ui/block_edit/3"' ) !== false, 'link to edit a nested block' );
check( strpos( $html, '(linked: layout 2, zone header)' ) !== false, 'a linked zone names its source' );
check( strpos( $html, 'pre_footer</td><td colspan="5">empty' ) !== false, 'an empty zone says so' );
check( strpos( $html, 'Available blocks (' ) !== false, 'the available blocks' );
check( preg_match( '#aside</td><td>Orphan</td>#', $html ) === 1, 'a block whose parent is not in the zone is at the top level' );
check( strpos( $html, 'Header / Footer' ) === false, 'another prepared layout is not the page\'s' );

$data = $panel->data();
check( $data === array( 'layout_id' => 27, 'identifier' => 'landing', 'rule_id' => 17, 'source' => 'rule', 'zones' => 4, 'blocks' => 4 ),
       'the data: ' . json_encode( $data ) );

// the default layout, and the same from the resolver cache
pageResolved( 'default', 0, 27 );
check( strpos( $panel->chip()['title'], 'the default layout (explayouts.ini' ) !== false, 'the default layout is named as such' );
check( strpos( $panel->html(), 'rule_edit' ) === false, 'no rule link without a rule' );
pageResolved( 'cache', 0, 27 );
check( strpos( $panel->chip()['title'], 'the default layout (from the resolver cache)' ) !== false, 'the cached default layout: ' . $panel->chip()['title'] );
pageResolved( 'cache', 17, 27 );
check( strpos( $panel->chip()['title'], 'Rule 17 (from the resolver cache)' ) === 0, 'a cached rule' );
pageResolved( 'none', 0, 0 );
check( $panel->chip()['text'] === 'Layout: none', 'no layout: ' . $panel->chip()['text'] );

// the links into the administration
$ini = eZINI::instance( 'explayouts.ini' );
$saved = $ini->hasVariable( 'DebugBarSettings', 'AdminURL' ) ? $ini->variable( 'DebugBarSettings', 'AdminURL' ) : null;
$ini->setVariable( 'DebugBarSettings', 'AdminURL', 'https://admin.example.com/' );
check( expLayoutsDebugBarPanel::adminURL( 'explayouts_ui/layout_edit/5' ) === 'https://admin.example.com/explayouts_ui/layout_edit/5', 'AdminURL is used when set' );
$ini->setVariable( 'DebugBarSettings', 'AdminURL', '' );
check( preg_match( '#/admin/explayouts_ui/layout_edit/5$#', expLayoutsDebugBarPanel::adminURL( 'explayouts_ui/layout_edit/5' ) ) === 1,
       'else the admin siteaccess after the site directory: ' . expLayoutsDebugBarPanel::adminURL( 'explayouts_ui/layout_edit/5' ) );
if ( $saved === null )
    $ini->removeSetting( 'DebugBarSettings', 'AdminURL' );
else
    $ini->setVariable( 'DebugBarSettings', 'AdminURL', $saved );

// A persistent worker serves the next request with the same statics: what the request before resolved and prepared
// is not shown for it.
$requestTime = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? $_SERVER['REQUEST_TIME_FLOAT'] : null;
$_SERVER['REQUEST_TIME_FLOAT'] = 1000.25;
expLayoutsResolver::forgetEarlierRequests();
expLayoutsRenderer::forgetEarlierRequests();
setStatic( 'expLayoutsResolver', 'resolutions', array( 'home' => array( 'path' => 'home', 'siteaccess' => 'site', 'source' => 'rule', 'rule_id' => 5, 'layout_id' => 2 ) ) );
setStatic( 'expLayoutsRenderer', 'prepared', array( 2 => array( 'id' => 2, 'identifier' => 'x', 'name' => 'X', 'layout_type' => 't', 'zones' => array() ) ) );
check( expLayoutsResolver::pageResolution() !== null && expLayoutsRenderer::prepared( 2 ) !== null, 'the same request keeps its resolution and prepared layout' );
$_SERVER['REQUEST_TIME_FLOAT'] = 1001.5;
check( expLayoutsResolver::pageResolution() === null, 'the next request of a persistent worker does not see the resolution of the request before' );
check( expLayoutsRenderer::prepared( 2 ) === null, 'nor the layout the request before prepared' );
if ( $requestTime === null )
    unset( $_SERVER['REQUEST_TIME_FLOAT'] );
else
    $_SERVER['REQUEST_TIME_FLOAT'] = $requestTime;

setStatic( 'expLayoutsResolver', 'resolutions', array() );
setStatic( 'expLayoutsRenderer', 'prepared', array() );

if ( $failures > 0 )
{
    echo "$failures check(s) failed\n";
    exit( 1 );
}
echo "OK\n";
