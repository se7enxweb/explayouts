<?php
/**
 * The gallery blocks (gallery, slider, thumb_gallery, grid_gallery, sushi_bar, logos), the carousel and the items of
 * a manual collection, without the kernel: nodes, collections and image attributes are stand-ins
 * (fixtures/gallerystandins.php, loaded in each test's own process), the settings are this extension's own
 * settings/explayouts.ini.append.php.
 *
 * Run with:
 *   vendor/bin/phpunit --no-configuration --bootstrap vendor/autoload.php extension/explayouts/tests/GalleryBlocksTest.php
 *
 * @license GPL-2.0-or-later
 */
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
#[RunTestsInSeparateProcesses]
class GalleryBlocksTest extends TestCase
{
    protected $image;

    protected function setUp(): void
    {
        require_once __DIR__ . '/fixtures/gallerystandins.php';

        $this->image = new gbImage( array(
            'small' => array( 'url' => 'var/site/storage/images/a/a_small.jpg' ),
            'original' => array( 'url' => 'var/site/storage/images/a/a.jpg' ),
        ) );
    }

    protected function node( $nodeId, $name, $imageContent, array $extra = array() )
    {
        $dataMap = $imageContent === null ? array() : array( 'image' => new gbAttribute( $imageContent ) );
        $node = new eZContentObjectTreeNode( $extra + array(
            'node_id' => $nodeId,
            'contentobject_id' => $nodeId + 1000,
            'name' => $name,
            'url_alias' => 'media/' . strtolower( $name ),
            'object' => new gbObject( $dataMap ),
        ) );
        eZContentObjectTreeNode::$nodes[$nodeId] = $node;
        return $node;
    }

    protected function manualCollection( $blockId, $collectionId, array $nodeIds, $limit = 0 )
    {
        expLayoutsCollection::$byBlock[$blockId] = new expLayoutsCollection( array( 'id' => $collectionId, 'collection_type' => 'manual', 'limit_value' => $limit ) );
        expLayoutsCollectionItem::$byCollection[$collectionId] = array();
        foreach ( $nodeIds as $nodeId )
            expLayoutsCollectionItem::$byCollection[$collectionId][] = new expLayoutsCollectionItem( array( 'value_id' => $nodeId ) );
    }

    public function testAnImageAttributeWhoseContentIsNotAnImageGivesAnItemWithoutImage()
    {
        // An object relation list under the identifier "image" has an array as content: hasAttribute() on it was
        // "Call to a member function hasAttribute() on array", and the page failed.
        eZContentObjectTreeNode::$children[10] = array(
            $this->node( 11, 'Picture', $this->image ),
            $this->node( 12, 'Related', array( 'relation_list' => array() ) ),
            $this->node( 13, 'Plain', null ),
        );

        $values = ( new expLayoutsGalleryBlockHandler() )->getValues( array( 'definition_identifier' => 'gallery', 'parameters' => array( 'parent_node_id' => 10 ) ) );

        $this->assertCount( 3, $values['items'] );
        $this->assertCount( 1, $values['images'] );
        $this->assertFalse( $values['items'][1]['has_image'] );
        $this->assertSame( '', $values['items'][1]['url'] );
    }

    public function testTheCarouselTakesAnImageAttributeWhoseContentIsNotAnImageAsNoImage()
    {
        eZContentObjectTreeNode::$children[10] = array(
            $this->node( 11, 'Picture', $this->image ),
            $this->node( 12, 'Related', array( 'relation_list' => array() ) ),
        );

        $values = ( new expLayoutsCarouselBlockHandler() )->getValues( array( 'parameters' => array( 'parent_node_id' => 10 ) ) );

        $this->assertSame( '/var/site/storage/images/a/a.jpg', $values['slides'][0]['image_url'] );
        $this->assertSame( '', $values['slides'][1]['image_url'] );
    }

    public function testAnItemCarriesWhatThePreviewAndTheLightboxRead()
    {
        eZContentObjectTreeNode::$children[10] = array( $this->node( 11, 'Picture', $this->image ) );

        $item = ( new expLayoutsGalleryBlockHandler() )->getValues( array( 'parameters' => array( 'parent_node_id' => 10 ) ) )['items'][0];

        $this->assertSame( 'Picture', $item['name'] );
        $this->assertSame( 11, $item['node_id'] );
        $this->assertSame( '/var/site/storage/images/a/a_small.jpg', $item['url'] );
        $this->assertSame( '/var/site/storage/images/a/a.jpg', $item['full_url'] );
        $this->assertSame( 'media/picture', $item['link'] );
    }

    public function testWithoutTheThumbnailAliasTheOriginalIsShown()
    {
        $original = new gbImage( array( 'original' => array( 'url' => 'var/site/storage/images/b/b.jpg' ) ) );
        eZContentObjectTreeNode::$children[10] = array( $this->node( 11, 'Picture', $original ) );

        $item = ( new expLayoutsGalleryBlockHandler() )->getValues( array( 'parameters' => array( 'parent_node_id' => 10, 'thumbnail_size' => 'tiny' ) ) )['items'][0];

        $this->assertTrue( $item['has_image'] );
        $this->assertSame( '/var/site/storage/images/b/b.jpg', $item['url'] );
        $this->assertSame( '/var/site/storage/images/b/b.jpg', $item['full_url'] );
    }

    public function testAManualCollectionShowsAllItsItemsUnlessItIsLimited()
    {
        $ids = array();
        for ( $i = 0; $i < 14; $i++ )
        {
            $this->node( 100 + $i, 'Item ' . $i, $this->image );
            $ids[] = 100 + $i;
        }
        $this->manualCollection( 5, 50, $ids );
        $handler = new expLayoutsGalleryBlockHandler();

        $values = $handler->getValues( array( 'id' => 5, 'definition_identifier' => 'slider', 'parameters' => array() ) );
        $this->assertCount( 14, $values['items'] );
        $this->assertSame( 1, expLayoutsCollection::$fetches, 'the collection is fetched once per render' );

        expLayoutsCollection::$byBlock[5]->attributes['limit_value'] = 4;
        $this->assertCount( 4, $handler->getValues( array( 'id' => 5, 'parameters' => array() ) )['items'] );
    }

    public function testAManualCollectionShowsOnlyWhatTheVisitorMaySee()
    {
        // eZContentObjectTreeNode::fetch() checks no permission: an item the visitor may not read, and a hidden one,
        // were shown to everybody.
        $this->node( 21, 'Readable', $this->image );
        $this->node( 22, 'Forbidden', $this->image, array( 'can_read' => false ) );
        $this->node( 23, 'Hidden', $this->image, array( 'is_invisible' => true ) );
        $this->manualCollection( 6, 60, array( 21, 22, 23, 99 ) );

        $names = array_column( ( new expLayoutsGalleryBlockHandler() )->getValues( array( 'id' => 6, 'parameters' => array() ) )['items'], 'name' );
        $this->assertSame( array( 'Readable' ), $names );

        $nodes = ( new expLayoutsManualQueryHandler() )->fetch( array( 'collection_id' => 60 ) )['items'];
        $this->assertSame( array( 21 ), array_map( function ( $node ) { return $node->attribute( 'node_id' ); }, $nodes ) );

        // where the siteaccess shows hidden nodes (the administration), a hidden item is shown, an unreadable one not
        eZContentObjectTreeNode::$showInvisible = true;
        $names = array_column( ( new expLayoutsGalleryBlockHandler() )->getValues( array( 'id' => 6, 'parameters' => array() ) )['items'], 'name' );
        $this->assertSame( array( 'Readable', 'Hidden' ), $names );
    }

    public function testTheGalleryViewType()
    {
        $this->assertSame( 'grid_gallery', expLayoutsGalleryBlockHandler::galleryViewType( array( 'definition_identifier' => 'gallery', 'view_type' => 'grid_gallery' ) ) );
        $this->assertSame( 'slider', expLayoutsGalleryBlockHandler::galleryViewType( array( 'definition_identifier' => 'slider', 'view_type' => 'default' ) ) );
        $this->assertSame( 'gallery', expLayoutsGalleryBlockHandler::galleryViewType( array( 'definition_identifier' => 'logos', 'view_type' => 'default' ) ) );
        $this->assertSame( 'sushi_bar', expLayoutsGalleryBlockHandler::galleryViewType( array( 'definition_identifier' => 'sushi_bar', 'view_type' => '../title' ) ) );
        $this->assertSame( 'thumb_gallery', ( new expLayoutsGalleryBlockHandler() )->getValues( array( 'definition_identifier' => 'gallery', 'view_type' => 'thumb_gallery', 'parameters' => array() ) )['view_type'] );
    }

    public function testThumbnailsPerRowAndLightbox()
    {
        $handler = new expLayoutsGalleryBlockHandler();

        $values = $handler->getValues( array( 'parameters' => array() ) );
        $this->assertSame( 6, $values['number_of_thumbnails'] );
        $this->assertFalse( $values['enable_lightbox'] );

        $values = $handler->getValues( array( 'parameters' => array( 'number_of_thumbnails' => '40', 'enable_lightbox' => '1' ) ) );
        $this->assertSame( 12, $values['number_of_thumbnails'] );
        $this->assertTrue( $values['enable_lightbox'] );

        $values = $handler->getValues( array( 'parameters' => array( 'number_of_thumbnails' => '3', 'enable_lightbox' => '0' ) ) );
        $this->assertSame( 3, $values['number_of_thumbnails'] );
        $this->assertFalse( $values['enable_lightbox'], 'the stored "0" of an unchecked box is off' );

        $parameters = $handler->getParameters();
        $this->assertSame( 'checkbox', $parameters['enable_lightbox']['type'] );
        // limited to the gallery view types, so the editor leaves both out for a logos block
        $this->assertSame( 'gallery,slider,thumb_gallery,grid_gallery,sushi_bar', $parameters['number_of_thumbnails']['view_type'] );
        $this->assertSame( 'gallery,slider,thumb_gallery,grid_gallery,sushi_bar', $parameters['enable_lightbox']['view_type'] );
    }

    public function testTheDesignParametersOfTheReferenceForItsViewTypes()
    {
        $handler = new expLayoutsGalleryBlockHandler();
        $parameters = $handler->getParameters();
        // valid_parameters of the reference's gallery view types (the autoplay time also for the slider)
        $expected = array(
            'next_and_previous' => 'slider,thumb_gallery,sushi_bar',
            'show_pagination' => 'slider,sushi_bar',
            'infinite_loop' => 'slider,thumb_gallery,sushi_bar',
            'transition' => 'slider,thumb_gallery',
            'autoplay' => 'slider,thumb_gallery,sushi_bar',
            'thumbnails_to_move' => 'sushi_bar',
            'show_details' => 'thumb_gallery',
        );
        foreach ( $expected as $name => $viewTypes )
            $this->assertSame( $viewTypes, $parameters[$name]['view_type'], $name );
        $this->assertSame( array( 'slide', 'fade' ), array_keys( $parameters['transition']['options'] ) );
        $this->assertSame( array( 1, 12 ), array( $parameters['autoplay']['children']['autoplay_time']['min'], $parameters['autoplay']['children']['autoplay_time']['max'] ) );
        $this->assertArrayHasKey( 'show_details_on_hover', $parameters['show_details']['children'] );

        $values = $handler->getValues( array( 'parameters' => array() ) );
        $this->assertTrue( $values['next_and_previous'] );
        $this->assertTrue( $values['infinite_loop'] );
        $this->assertFalse( $values['autoplay'] );
        $this->assertSame( 'slide', $values['transition'] );
        $this->assertSame( 5, $values['autoplay_time'] );
        $this->assertSame( 1, $values['thumbnails_to_move'] );

        $values = $handler->getValues( array( 'parameters' => array( 'next_and_previous' => '0', 'transition' => 'fade"', 'autoplay' => '1',
            'autoplay_time' => '40', 'thumbnails_to_move' => '3', 'show_details' => '1', 'show_details_on_hover' => '1' ) ) );
        $this->assertFalse( $values['next_and_previous'] );
        $this->assertSame( 'slide', $values['transition'], 'only slide or fade' );
        $this->assertTrue( $values['autoplay'] );
        $this->assertSame( 12, $values['autoplay_time'] );
        $this->assertSame( 3, $values['thumbnails_to_move'] );
        $this->assertTrue( $values['show_details_on_hover'] );
    }

    public function testEveryGalleryDefinitionOffersAllGalleryViewTypesItsOwnFirst()
    {
        $ini = file_get_contents( dirname( __DIR__ ) . '/settings/explayouts.ini.append.php' );
        $expected = expLayoutsGalleryBlockHandler::$viewTypes;
        sort( $expected );

        foreach ( expLayoutsGalleryBlockHandler::$viewTypes as $definition )
        {
            $this->assertSame( 1, preg_match( '/^\[BlockDefinition_' . $definition . '\]\n(.*?)(?=^\[)/ms', $ini, $m ), $definition );
            preg_match_all( '/^ViewTypes\[\]=(\w+)$/m', $m[1], $viewTypes );
            preg_match_all( '/^ViewTypeNames\[(\w+)\]=.+$/m', $m[1], $names );
            $this->assertSame( $definition, $viewTypes[1][0], $definition );
            $listed = $viewTypes[1];
            sort( $listed );
            $named = $names[1];
            sort( $named );
            $this->assertSame( $expected, $listed, $definition );
            $this->assertSame( $expected, $named, $definition );
            $this->assertStringContainsString( 'Handler=expLayoutsGalleryBlockHandler', $m[1] );
        }
    }

    public function testABlockWithItemsInItsManualCollectionAndNoParentShowsThem()
    {
        eZContentObjectTreeNode::$children[10] = array( $this->node( 11, 'Child', $this->image ), $this->node( 12, 'Other child', null ) );
        $this->node( 31, 'Picked', $this->image );
        $this->manualCollection( 7, 70, array( 31 ) );
        $carousel = new expLayoutsCarouselBlockHandler();

        $values = $carousel->getValues( array( 'id' => 7, 'parameters' => array() ) );
        $this->assertSame( array( 'Picked' ), array_map( function ( $node ) { return $node->attribute( 'name' ); }, $values['items'] ) );
        $this->assertSame( array( 'manual' ), expLayoutsQueryHandlerFactory::$asked );
        $this->assertSame( 1, expLayoutsCollection::$fetches, 'the collection is fetched once per render' );
        $this->assertSame( 1, expLayoutsCollectionItem::$fetches, 'its items are fetched once and handed on' );
        $this->assertNotContains( 'dq', ezpHttpCacheListener::$tags, 'a manual collection is no query' );
        $this->assertContains( 'l31', ezpHttpCacheListener::$tags );
    }

    public function testAParentNodeComesBeforeTheManualCollection()
    {
        eZContentObjectTreeNode::$children[10] = array( $this->node( 11, 'Child', $this->image ), $this->node( 12, 'Other child', null ) );
        $this->node( 31, 'Picked', $this->image );
        $this->manualCollection( 7, 70, array( 31 ) );
        $carousel = new expLayoutsCarouselBlockHandler();

        $values = $carousel->getValues( array( 'id' => 7, 'parameters' => array( 'parent_node_id' => 10 ) ) );
        $this->assertCount( 2, $values['items'] );
        $this->assertSame( array( 'children' ), expLayoutsQueryHandlerFactory::$asked );
        $this->assertContains( 'dq', ezpHttpCacheListener::$tags );
        $this->assertContains( 'pl10', ezpHttpCacheListener::$tags );

        expLayoutsQueryHandlerFactory::$asked = array();
        $values = $carousel->getValues( array( 'id' => 7, 'parameters' => array( 'query_type' => 'children', 'node_id' => 10 ) ) );
        $this->assertCount( 2, $values['items'] );
        $this->assertSame( array( 'children' ), expLayoutsQueryHandlerFactory::$asked, 'an explicit query_type wins' );

        expLayoutsCollectionItem::$byCollection[70] = array();
        expLayoutsQueryHandlerFactory::$asked = array();
        $this->assertCount( 0, $carousel->getValues( array( 'id' => 7, 'parameters' => array() ) )['items'] );
        $this->assertSame( array( 'children' ), expLayoutsQueryHandlerFactory::$asked, 'an empty manual collection is no source' );
    }
}
