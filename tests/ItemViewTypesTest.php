<?php
/**
 * The item view types of a block definition (expLayoutsBlockHandlerFactory::itemViewTypes() and getBlockInfo()):
 * the handler's own list, then ItemViewTypes[] of [BlockDefinition_<identifier>], read from the settings of the
 * siteaccess useSiteAccess() chose. The item view types per view type (ViewTypeItemViewTypes[]) and the parameters the
 * editor does not offer (HiddenParameters[]). And the list the extension ships for the list block.
 *
 * Without the kernel: eZINI and eZSiteAccess are stand-ins declared in each test's own process (setUp()), holding the
 * settings as arrays.
 *
 * Run with:
 *   vendor/bin/phpunit --no-configuration --bootstrap vendor/autoload.php extension/explayouts/tests/ItemViewTypesTest.php
 *
 * @license GPL-2.0-or-later
 */
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

function itemViewTypesTestStandIns()
{
    if ( !class_exists( 'eZINI', false ) )
    {
        class eZINI
        {
            /** explayouts.ini of the current siteaccess, as group => variable => value */
            public static $current = array();
            /** explayouts.ini of other siteaccesses, as siteaccess => group => variable => value */
            public static $siteAccesses = array();
            public $groups;
            public function __construct( array $groups ) { $this->groups = $groups; }
            public static function instance( $file = 'site.ini' ) { return new self( self::$current ); }
            public function hasGroup( $group ) { return isset( $this->groups[$group] ); }
            public function hasVariable( $group, $name ) { return isset( $this->groups[$group][$name] ); }
            public function variable( $group, $name ) { return $this->groups[$group][$name]; }
        }
    }
    if ( !class_exists( 'eZSiteAccess', false ) )
    {
        class eZSiteAccess
        {
            public static function current() { return array( 'name' => 'admin' ); }
            public static function getIni( $siteAccess, $file ) { return new eZINI( eZINI::$siteAccesses[$siteAccess] ); }
        }
    }
    if ( !class_exists( 'ivtListHandler', false ) )
    {
        class ivtListHandler
        {
            public function getItemViewTypes() { return array( 'standard' => 'Standard', 'line' => 'Line' ); }
        }
        class ivtPlainHandler
        {
        }
    }
    require_once dirname( __DIR__ ) . '/classes/explayoutsblockhandlerfactory.php';
}

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
#[RunTestsInSeparateProcesses]
class ItemViewTypesTest extends TestCase
{
    protected function setUp(): void
    {
        itemViewTypesTestStandIns();
        eZINI::$current = array(
            'BlockDefinition_list' => array(
                'Handler' => 'ivtListHandler',
                'ViewTypes' => array( 'list', 'grid', 'list' ),
                'ItemViewTypes' => array( 'zigzag' => 'Zig-zag', 'line' => 'Line (short)', 0 => 'tile' ),
            ),
            'BlockDefinition_plain' => array( 'Handler' => 'ivtPlainHandler' ),
            'BlockDefinition_gallery' => array( 'Handler' => 'ivtPlainHandler', 'ItemViewTypes' => array( 'slide' => 'Slide' ),
                // as Netgen Layouts' item_view_types per view type; malformed entries are left out
                'ViewTypeItemViewTypes' => array( 'slider' => 'slide', 'sushi_bar' => ' overlay, line ,overlay', 'empty' => ' , ', 0 => 'slide' ),
                'HiddenParameters' => array( 'image_attribute', ' thumbnail_size ', '', 'image_attribute' ),
                'ViewTypes' => array( 'gallery', 'slider', 'sushi_bar', 'carousel' ),
                'HiddenViewTypes' => array( 'gallery', ' sushi_bar' ),
                'ViewTypeHiddenParameters' => array( 'slider' => 'number_of_thumbnails, enable_lightbox', 'carousel' => ' ', 0 => 'x' ) ),
            'BlockDefinition_quote' => array( 'Handler' => 'ivtPlainHandler', 'ViewTypes' => array( 'quote' ), 'HiddenViewTypes' => array( 'quote' ) ),
        );
        eZINI::$siteAccesses = array(
            'site' => array(
                'BlockDefinition_list' => array( 'Handler' => 'ivtListHandler', 'ItemViewTypes' => array( 'grid_tile' => 'Grid tile' ) ),
            ),
        );
    }

    public function testTheHandlersListComesFirstThenTheSettings()
    {
        $this->assertSame(
            array( 'standard' => 'Standard', 'line' => 'Line (short)', 'zigzag' => 'Zig-zag', 'tile' => 'tile' ),
            expLayoutsBlockHandlerFactory::itemViewTypes( 'list' )
        );
    }

    public function testADefinitionWithNeitherHasNone()
    {
        $this->assertSame( array(), expLayoutsBlockHandlerFactory::itemViewTypes( 'plain' ) );
        $this->assertSame( array(), expLayoutsBlockHandlerFactory::itemViewTypes( 'no_such_block' ) );
    }

    public function testTheSettingsAloneGiveAHandlerWithoutAListItemViewTypes()
    {
        $this->assertSame( array( 'slide' => 'Slide' ), expLayoutsBlockHandlerFactory::itemViewTypes( 'gallery' ) );
    }

    public function testTheBlockInfoCarriesThemAndEachViewTypeOnce()
    {
        $info = expLayoutsBlockHandlerFactory::getBlockInfo( 'list' );
        $this->assertSame( array( 'list', 'grid' ), $info['view_types'] );
        $this->assertSame( expLayoutsBlockHandlerFactory::itemViewTypes( 'list' ), $info['item_view_types'] );
    }

    public function testTheItemViewTypesOfEachViewTypeWhereTheSettingsLimitThem()
    {
        $this->assertSame( array( 'slider' => array( 'slide' ), 'sushi_bar' => array( 'overlay', 'line' ) ),
                           expLayoutsBlockHandlerFactory::viewTypeItemViewTypes( 'gallery' ) );
        $this->assertSame( array(), expLayoutsBlockHandlerFactory::viewTypeItemViewTypes( 'list' ), 'no limit: every view type offers all' );
        $info = expLayoutsBlockHandlerFactory::getBlockInfo( 'gallery' );
        $this->assertSame( expLayoutsBlockHandlerFactory::viewTypeItemViewTypes( 'gallery' ), $info['view_type_item_view_types'] );
    }

    public function testTheParametersTheEditorDoesNotOffer()
    {
        $this->assertSame( array( 'image_attribute', 'thumbnail_size' ), expLayoutsBlockHandlerFactory::hiddenParameters( 'gallery' ) );
        $this->assertSame( array(), expLayoutsBlockHandlerFactory::hiddenParameters( 'list' ) );
        $info = expLayoutsBlockHandlerFactory::getBlockInfo( 'gallery' );
        $this->assertSame( array( 'image_attribute', 'thumbnail_size' ), $info['hidden_parameters'] );
    }

    public function testTheParametersAViewTypeDoesNotOffer()
    {
        $this->assertSame( array( 'slider' => array( 'number_of_thumbnails', 'enable_lightbox' ) ),
                           expLayoutsBlockHandlerFactory::viewTypeHiddenParameters( 'gallery' ) );
        $this->assertSame( array(), expLayoutsBlockHandlerFactory::viewTypeHiddenParameters( 'list' ) );
        $this->assertSame( expLayoutsBlockHandlerFactory::viewTypeHiddenParameters( 'gallery' ),
                           expLayoutsBlockHandlerFactory::getBlockInfo( 'gallery' )['view_type_hidden_parameters'] );
    }

    public function testHiddenViewTypesAreNotOffered()
    {
        $this->assertSame( array( 'slider', 'carousel' ), expLayoutsBlockHandlerFactory::getBlockInfo( 'gallery' )['view_types'] );
        $this->assertSame( array( 'quote' ), expLayoutsBlockHandlerFactory::getBlockInfo( 'quote' )['view_types'], 'never none: hiding every one hides none' );
    }

    public function testTheSettingsOfAnotherSiteAccess()
    {
        $this->assertSame( 'site', expLayoutsBlockHandlerFactory::useSiteAccess( 'site' ) );
        $this->assertSame( array( 'standard' => 'Standard', 'line' => 'Line', 'grid_tile' => 'Grid tile' ),
                           expLayoutsBlockHandlerFactory::itemViewTypes( 'list' ) );

        // the current siteaccess, or none: its own settings again
        $this->assertSame( '', expLayoutsBlockHandlerFactory::useSiteAccess( 'admin' ) );
        $this->assertArrayHasKey( 'zigzag', expLayoutsBlockHandlerFactory::itemViewTypes( 'list' ) );
        expLayoutsBlockHandlerFactory::useSiteAccess( 'site' );
        $this->assertSame( '', expLayoutsBlockHandlerFactory::useSiteAccess( false ) );
        $this->assertArrayHasKey( 'zigzag', expLayoutsBlockHandlerFactory::itemViewTypes( 'list' ) );
    }

    public function testTheListBlockOffersTheItemViewsOfItsZigZagAndAccordionViewTypes()
    {
        $ini = (string)file_get_contents( dirname( __DIR__ ) . '/settings/explayouts.ini.append.php' );
        $this->assertSame( 1, preg_match( '/^\[BlockDefinition_list\]\n(.*?)(?=^\[)/ms', $ini, $m ) );
        $this->assertStringContainsString( "ItemViewTypes[zigzag]=Zig-zag\n", $m[1] );
        $this->assertStringContainsString( "ItemViewTypes[accordion]=Accordion\n", $m[1] );
        $this->assertStringContainsString( "ViewTypes[]=list_zigzag\n", $m[1] );
        $this->assertStringContainsString( "ViewTypes[]=list_accordion\n", $m[1] );
    }
}
