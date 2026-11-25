<?php
/**
 * The siteaccess an administration renders layout previews in (expLayoutsPreviewSiteAccess), without the kernel:
 * eZINI and eZSiteAccess are stand-ins holding the settings of three sites in one installation.
 *
 * Run directly with:
 *   php tests/explayoutspreviewsiteaccesstest.php
 *
 *  PV-01 - [PreviewSettings] SiteAccess of the administration wins
 *  PV-02 - without it FrontSiteAccess, then the first of RelatedSiteAccessList[] that is not an administration, then
 *          DefaultAccess - not DefaultAccess first (the default of the whole installation, another site's)
 *  PV-03 - a siteaccess with another database is never chosen; a configured one with another database is an error
 *          with a reason, not a fall back to another site
 *  PV-04 - the current siteaccess and administrations are skipped
 *  PV-05 - a layout's siteaccess (resolveForLayout()): the siteaccess condition of its rules wins
 *  PV-06 - without one, a node or subtree target: the site whose root node lies deepest above it; of a site in two
 *          languages on one root node, the one in the language asked for (default: the administration's)
 *  PV-07 - a layout without rules gets the preview siteaccess of the administration (resolve())
 *  PV-08 - a rule naming a siteaccess that does not qualify (another database, an administration) falls back to it too
 *  PV-09 - the parts on their own: conditions before targets, the deepest root node, the language order
 */

$failures = 0;
function pvCheck( $ok, $message )
{
    global $failures;
    echo ( $ok ? 'PASS' : 'FAIL' ), ": $message\n";
    if ( !$ok )
        $failures++;
}

class eZINI
{
    /** settings per siteaccess: name => file => group => variable => value */
    public static $settings = array();
    public $siteAccess, $file;
    public static function instance( $file = 'site.ini' ) { return new self( $GLOBALS['eZCurrentAccess']['name'], $file ); }
    public function __construct( $siteAccess, $file = 'site.ini' ) { $this->siteAccess = $siteAccess; $this->file = $file; }
    public function hasVariable( $group, $name ) { return isset( self::$settings[$this->siteAccess][$this->file][$group][$name] ); }
    public function variable( $group, $name ) { return $this->hasVariable( $group, $name ) ? self::$settings[$this->siteAccess][$this->file][$group][$name] : false; }
}
class eZSiteAccess
{
    public static function getIni( $name, $file = 'site.ini' ) { return new eZINI( $name, $file ); }
}

require_once dirname( __DIR__ ) . '/classes/explayoutspreviewsiteaccess.php';

$site = function( $database, $design, array $extra = array() )
{
    return array( 'site.ini' => array_replace_recursive( array(
        'DatabaseSettings' => array( 'DatabaseImplementation' => 'ezmysqli', 'Server' => 'localhost', 'Port' => '', 'Database' => $database ),
        'DesignSettings' => array( 'SiteDesign' => $design ),
        'SiteSettings' => array( 'DefaultAccess' => 'blog' ),
    ), $extra ) );
};
$reset = function() use ( $site )
{
    eZINI::$settings = array(
        'blog' => $site( 'db_blog', 'bootstrap5' ),
        'blog_admin' => $site( 'db_blog', 'admin4' ),
        'shop' => $site( 'db_shop', 'shop' ),
        'shop_en' => $site( 'db_shop', 'shop' ),
        'shop_admin' => $site( 'db_shop', 'admin4', array( 'SiteAccessSettings' => array( 'RelatedSiteAccessList' => array( 'shop_admin', 'shop', 'shop_en' ) ),
                                                           'SiteSettings' => array( 'FrontSiteAccess' => 'shop_en' ) ) ),
    );
    $GLOBALS['eZCurrentAccess'] = array( 'name' => 'shop_admin' );
    expLayoutsPreviewSiteAccess::$availableSiteAccesses = array_keys( eZINI::$settings );
};
$settings = function( $siteAccess, $file, $group, $name, $value ) { eZINI::$settings[$siteAccess][$file][$group][$name] = $value; };

// PV-02
$reset();
pvCheck( expLayoutsPreviewSiteAccess::resolve() === 'shop_en', 'PV-02 FrontSiteAccess, not the installation default blog' );
unset( eZINI::$settings['shop_admin']['site.ini']['SiteSettings']['FrontSiteAccess'] );
pvCheck( expLayoutsPreviewSiteAccess::resolve() === 'shop', 'PV-02 then the first related siteaccess that is not an administration' );
eZINI::$settings['shop_admin']['site.ini']['SiteAccessSettings']['RelatedSiteAccessList'] = array();
$answer = expLayoutsPreviewSiteAccess::resolve();
pvCheck( $answer === false && strpos( expLayoutsPreviewSiteAccess::$lastError, 'another database' ) !== false,
         'PV-03 DefaultAccess blog has another database: nothing, with the reason (' . expLayoutsPreviewSiteAccess::$lastError . ')' );

// PV-01, PV-03
$reset();
$settings( 'shop_admin', 'explayouts.ini', 'PreviewSettings', 'SiteAccess', 'shop' );
pvCheck( expLayoutsPreviewSiteAccess::resolve() === 'shop', 'PV-01 the configured siteaccess wins over FrontSiteAccess' );
$settings( 'shop_admin', 'explayouts.ini', 'PreviewSettings', 'SiteAccess', 'blog' );
pvCheck( expLayoutsPreviewSiteAccess::resolve() === false && strpos( expLayoutsPreviewSiteAccess::$lastError, "'blog'" ) !== false,
         'PV-03 a configured siteaccess with another database is an error, no fall back' );

// PV-04
$reset();
$settings( 'shop_admin', 'explayouts.ini', 'PreviewSettings', 'SiteAccess', 'shop_admin' );
pvCheck( expLayoutsPreviewSiteAccess::resolve() === false, 'PV-04 the current siteaccess is not its own preview' );
$reset();
$GLOBALS['eZCurrentAccess'] = array( 'name' => 'blog_admin' );
pvCheck( expLayoutsPreviewSiteAccess::resolve() === 'blog', 'PV-04 blog_admin previews in blog (DefaultAccess, same database)' );
pvCheck( expLayoutsPreviewSiteAccess::choose( array( 'blog_admin', 'shop_admin', 'blog' ), 'x', expLayoutsPreviewSiteAccess::databaseOf( new eZINI( 'blog' ) ),
                                              function( $name ) { return array( 'admin' => strpos( $name, '_admin' ) !== false,
                                                                                'database' => expLayoutsPreviewSiteAccess::databaseOf( new eZINI( $name ) ) ); } ) === 'blog',
         'PV-04 administrations are skipped' );

// PV-05 to PV-09: the siteaccess of a layout (resolveForLayout()). shop_admin also serves the site fair in English and
// German on root node 60, in the same database; shop and shop_en show the whole tree (root node 2).
$resetSites = function() use ( $reset, $site, $settings )
{
    $reset();
    eZINI::$settings['fair'] = $site( 'db_shop', 'fair' );
    eZINI::$settings['fair_ger'] = $site( 'db_shop', 'fair' );
    foreach ( array( 'shop' => 'eng-GB', 'shop_en' => 'eng-GB', 'fair' => 'eng-GB', 'fair_ger' => 'ger-DE', 'shop_admin' => 'eng-GB' ) as $name => $locale )
        $settings( $name, 'site.ini', 'RegionalSettings', 'ContentObjectLocale', $locale );
    $settings( 'fair', 'content.ini', 'NodeSettings', 'RootNode', 60 );
    $settings( 'fair_ger', 'content.ini', 'NodeSettings', 'RootNode', 60 );
    expLayoutsPreviewSiteAccess::$availableSiteAccesses = array_keys( eZINI::$settings );
    expLayoutsPreviewSiteAccess::$nodePaths = array( 61 => array( 1, 2, 60, 61 ), 70 => array( 1, 2, 70 ) );
    expLayoutsPreviewSiteAccess::$layoutRules = array(
        // a fair layout limited to the German site, as the rules of an imported site are
        1 => array( array( 'conditions' => array( array( 'siteaccess', '["fair_ger"]' ), array( 'class', '["ng_frontpage"]' ) ),
                           'targets' => array( array( 'subtree', '60' ) ) ) ),
        // a fair layout mapped to a node only
        2 => array( array( 'conditions' => array(), 'targets' => array( array( 'node', '61' ) ) ) ),
        // a shop layout mapped to a node only
        3 => array( array( 'conditions' => array(), 'targets' => array( array( 'subtree', '70' ), array( 'path_info_prefix', '/' ) ) ) ),
        // limited to a siteaccess of another database, and to an administration
        5 => array( array( 'conditions' => array( array( 'siteaccess', 'blog' ) ), 'targets' => array() ) ),
        6 => array( array( 'conditions' => array( array( 'siteaccess', '["shop_admin"]' ) ), 'targets' => array() ) ),
    );
};

// PV-05
$resetSites();
pvCheck( expLayoutsPreviewSiteAccess::resolveForLayout( 1 ) === 'fair_ger', 'PV-05 the siteaccess condition of the layout\'s rule wins over FrontSiteAccess shop_en' );

// PV-06
$resetSites();
pvCheck( expLayoutsPreviewSiteAccess::resolveForLayout( 2 ) === 'fair', 'PV-06 a node target: the site whose root node lies deepest above it, in the administration\'s language (eng-GB)' );
pvCheck( expLayoutsPreviewSiteAccess::resolveForLayout( 2, 'ger-DE' ) === 'fair_ger', 'PV-06 the same in German: fair_ger' );
pvCheck( expLayoutsPreviewSiteAccess::resolveForLayout( 3 ) === 'shop', 'PV-06 a node of the shop: shop (root node 2), not fair' );
// an editorial interface on root node 2 whose design is not admin*, but that requires a login, comes first in the list
$resetSites();
eZINI::$settings = array( 'shop_editor' => $site( 'db_shop', 'editor', array( 'SiteAccessSettings' => array( 'RequireUserLogin' => 'true' ),
                                                                           'RegionalSettings' => array( 'ContentObjectLocale' => 'eng-GB' ) ) ) ) + eZINI::$settings;
expLayoutsPreviewSiteAccess::$availableSiteAccesses = array_keys( eZINI::$settings );
pvCheck( expLayoutsPreviewSiteAccess::resolveForLayout( 3 ) === 'shop', 'PV-06 a siteaccess that requires a login (shop_editor) is not a site the node is shown in' );

// the editor works on the draft, a layout of its own whose rules are those of its published layout
$resetSites();
expLayoutsPreviewSiteAccess::$publishedLayouts = array( 101 => 1, 102 => 2, 1 => 1 );
pvCheck( expLayoutsPreviewSiteAccess::resolveForLayout( 101 ) === 'fair_ger', 'PV-06 a draft (101) takes the rules of its published layout (1): fair_ger' );
pvCheck( expLayoutsPreviewSiteAccess::resolveForLayout( 102 ) === 'fair', 'PV-06 a draft (102) of a layout mapped to a node: fair' );
expLayoutsPreviewSiteAccess::$publishedLayouts = null;

// PV-07
$resetSites();
expLayoutsPreviewSiteAccess::$publishedLayouts = array();
pvCheck( expLayoutsPreviewSiteAccess::resolveForLayout( 4 ) === 'shop_en', 'PV-07 a layout without rules gets the preview siteaccess of the administration (FrontSiteAccess)' );

// PV-08
$resetSites();
pvCheck( expLayoutsPreviewSiteAccess::resolveForLayout( 5 ) === 'shop_en', 'PV-08 a condition naming a siteaccess of another database falls back to the administration\'s' );
pvCheck( expLayoutsPreviewSiteAccess::resolveForLayout( 6 ) === 'shop_en', 'PV-08 a condition naming the administration itself falls back too' );

// PV-09
pvCheck( expLayoutsPreviewSiteAccess::siteAccessesOfRules( array(
             array( 'conditions' => array( array( 'class', '["x"]' ) ), 'targets' => array( array( 'node', '61' ) ) ),
             array( 'conditions' => array( array( 'siteaccess', 'fair' ) ), 'targets' => array() ),
             array( 'conditions' => array( array( 'siteaccess', '["fair_ger","fair"]' ) ), 'targets' => array() ) ),
             function( $id ) { return array( 'never' ); } ) === array( 'fair', 'fair_ger' ),
         'PV-09 siteaccess conditions (one name or a list) come before node targets, each name once' );
pvCheck( expLayoutsPreviewSiteAccess::deepestRoots( array( 1, 2, 60, 61 ), array( 'shop' => 2, 'fair' => 60, 'fair_ger' => 60, 'other' => 99 ) ) === array( 'fair', 'fair_ger' ),
         'PV-09 the deepest root node above the node, with every siteaccess on it' );
pvCheck( expLayoutsPreviewSiteAccess::byLanguage( array( 'fair', 'fair_ger' ), 'ger-DE', function( $n ) { return $n === 'fair_ger' ? 'ger-DE' : 'eng-GB'; } ) === array( 'fair_ger', 'fair' ),
         'PV-09 the one in the language comes first' );

echo $failures ? "$failures failed\n" : "all passed\n";
exit( $failures ? 1 : 0 );
