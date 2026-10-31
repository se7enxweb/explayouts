<?php
class expLayoutsGalleryBlockHandler implements expLayoutsBlockHandlerInterface
{
    /**
     * The gallery view types. Every gallery block offers all of them in the
     * Design tab, as the reference gallery block does, and a block renders
     * with the template of its view type. A block stored with the view type
     * 'default' (every block before the gallery view types existed) renders
     * as its own definition: a slider block as a slider.
     */
    public static $viewTypes = array( 'gallery', 'slider', 'thumb_gallery', 'grid_gallery', 'sushi_bar' );

    /** Seconds between two images with autoplay, as in the reference */
    const MIN_AUTOPLAY_TIME = 1;
    const MAX_AUTOPLAY_TIME = 12;

    public function getParameters()
    {
        return array(
            'parent_node_id' => array(
                'name' => 'Parent node ID',
                'type' => 'integer',
                'default' => 0,
            ),
            'limit' => array(
                'name' => 'Limit',
                'type' => 'integer',
                'default' => 12,
            ),
            'class_filter' => array(
                'name' => 'Class filter',
                'type' => 'string',
                'default' => '',
            ),
            // Only for the gallery view types: the editor leaves a parameter out
            // for a block none of whose view types it names (logos uses this
            // handler too, and its template has no columns and no lightbox).
            'number_of_thumbnails' => array(
                'name' => 'Thumbnails per row/slide',
                'type' => 'integer',
                'default' => 6,
                'view_type' => 'gallery,slider,thumb_gallery,grid_gallery,sushi_bar',
            ),
            'enable_lightbox' => array(
                'name' => 'Enable lightbox',
                'type' => 'checkbox',
                'default' => '',
                'view_type' => 'gallery,slider,thumb_gallery,grid_gallery,sushi_bar',
            ),
            // The design parameters of the reference's gallery block, each limited to the view types the reference
            // offers it for (valid_parameters in its block_definitions.yaml); the gallery view type of this port
            // takes the ones of the grid gallery. Autoplay time is offered for the slider too, which the reference
            // leaves out although its slider plays automatically.
            'next_and_previous' => array(
                'name' => 'Show next and previous buttons',
                'type' => 'checkbox',
                'default' => '1',
                'view_type' => 'slider,thumb_gallery,sushi_bar',
            ),
            'show_pagination' => array(
                'name' => 'Show pagination',
                'type' => 'checkbox',
                'default' => '',
                'view_type' => 'slider,sushi_bar',
            ),
            'infinite_loop' => array(
                'name' => 'Infinite loop',
                'type' => 'checkbox',
                'default' => '1',
                'view_type' => 'slider,thumb_gallery,sushi_bar',
            ),
            'transition' => array(
                'name' => 'Transition',
                'type' => 'select',
                'options' => array(
                    'slide' => 'Slide',
                    'fade' => 'Fade',
                ),
                'default' => 'slide',
                'view_type' => 'slider,thumb_gallery',
            ),
            'autoplay' => array(
                'name' => 'Autoplay',
                'type' => 'compound_checkbox',
                'default' => '',
                'view_type' => 'slider,thumb_gallery,sushi_bar',
                'children' => array(
                    'autoplay_time' => array(
                        'name' => 'Autoplay time (seconds, 1-12)',
                        'type' => 'integer',
                        'default' => 5,
                        'min' => self::MIN_AUTOPLAY_TIME,
                        'max' => self::MAX_AUTOPLAY_TIME,
                    ),
                ),
            ),
            'thumbnails_to_move' => array(
                'name' => 'Thumbnails to move',
                'type' => 'integer',
                'default' => 1,
                'min' => 1,
                'view_type' => 'sushi_bar',
            ),
            'show_details' => array(
                'name' => 'Show details',
                'type' => 'compound_checkbox',
                'default' => '',
                'view_type' => 'thumb_gallery',
                'children' => array(
                    'show_details_on_hover' => array(
                        'name' => 'Show details on hover',
                        'type' => 'checkbox',
                        'default' => '',
                    ),
                ),
            ),
            'image_attribute' => array(
                'name' => 'Image attribute identifier',
                'type' => 'string',
                'default' => 'image',
            ),
            'thumbnail_size' => array(
                'name' => 'Thumbnail size alias',
                'type' => 'string',
                'default' => 'small',
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        $parentNodeId = isset( $params['parent_node_id'] ) ? (int)$params['parent_node_id'] : 0;
        $limit = isset( $params['limit'] ) ? (int)$params['limit'] : 12;
        $classFilter = isset( $params['class_filter'] ) ? trim( $params['class_filter'] ) : '';
        $imageAttribute = isset( $params['image_attribute'] ) ? $params['image_attribute'] : 'image';
        $thumbnailSize = isset( $params['thumbnail_size'] ) ? $params['thumbnail_size'] : 'small';
        $numberOfThumbnails = isset( $params['number_of_thumbnails'] ) && (int)$params['number_of_thumbnails'] > 0
            ? min( 12, (int)$params['number_of_thumbnails'] )
            : 6;

        $items = array();
        $images = array();
        if ( $parentNodeId > 0 )
        {
            try
            {
                $nodes = eZContentObjectTreeNode::subTreeByNodeID( array(
                    'Depth' => 1,
                    'Limit' => $limit,
                    'SortBy' => array( 'published', false ),
                ), $parentNodeId );
                if ( is_array( $nodes ) )
                {
                    foreach ( $nodes as $item )
                    {
                        $object = $item->attribute( 'object' );
                        if ( !$object )
                            continue;
                        $entry = self::buildItem( $item, $object, $imageAttribute, $thumbnailSize );
                        $items[] = $entry;
                        if ( $entry['has_image'] )
                            $images[] = $entry;
                    }
                }
            }
            catch ( Exception $e )
            {
            }
        }

        // The block's collection, fetched once for both cases below.
        $collection = count( $items ) === 0 && isset( $block['id'] ) ? expLayoutsCollection::fetchByBlock( (int)$block['id'] ) : null;

        // dynamic collections imported from the Nexus reference (queries) come first
        if ( count( $items ) === 0 && $collection )
        {
            $dynCollection = $collection;
            if ( $dynCollection->attribute( 'collection_type' ) === 'dynamic' )
            {
                $dynResult = expLayoutsDynamicCollection::fetch( $dynCollection );
                if ( is_array( $dynResult ) )
                {
                    foreach ( $dynResult['items'] as $item )
                    {
                        $object = $item->attribute( 'object' );
                        if ( !$object )
                            continue;
                        $entry = self::buildItem( $item, $object, $imageAttribute, $thumbnailSize );
                        $items[] = $entry;
                        if ( $entry['has_image'] )
                            $images[] = $entry;
                    }
                }
            }
        }

        if ( count( $items ) === 0 && $collection )
        {
            // A manual collection shows all its items unless the collection
            // itself is limited; the block's limit parameter (default 12)
            // belongs to the parent node query above, and the editor no
            // longer offers it for a block with a collection.
            $limit = (int)$collection->attribute( 'limit_value' ) > 0
                ? (int)$collection->attribute( 'limit_value' )
                : ( isset( $params['limit'] ) && (int)$params['limit'] > 0 ? (int)$params['limit'] : PHP_INT_MAX );
            $collectionItems = expLayoutsCollectionItem::fetchByCollection( (int)$collection->attribute( 'id' ) );
            foreach ( $collectionItems as $collectionItem )
            {
                $nodeId = (int)$collectionItem->attribute( 'value_id' );
                if ( $nodeId <= 0 )
                    continue;
                $item = eZContentObjectTreeNode::fetch( $nodeId );
                // fetch() checks no permission: an item the visitor may
                // not read, or a hidden one, was shown to everybody
                if ( !expLayoutsManualQueryHandler::isShownNode( $item ) )
                    continue;
                $object = $item->attribute( 'object' );
                if ( !$object )
                    continue;
                if ( $classFilter !== '' )
                {
                    $filterArray = array_filter( array_map( 'trim', explode( ',', $classFilter ) ) );
                    if ( !empty( $filterArray ) && !in_array( $item->attribute( 'class_identifier' ), $filterArray ) )
                        continue;
                }
                $entry = self::buildItem( $item, $object, $imageAttribute, $thumbnailSize );
                $items[] = $entry;
                if ( $entry['has_image'] )
                    $images[] = $entry;
                if ( count( $items ) >= $limit )
                    break;
            }
        }

        return array(
            'total' => count( $items ),
            'images' => $images,
            'items' => $items,
            'thumbnail_size' => $thumbnailSize,
            'view_type' => self::galleryViewType( $block ),
            'number_of_thumbnails' => $numberOfThumbnails,
            'enable_lightbox' => self::isOn( $params, 'enable_lightbox', false ),
            'next_and_previous' => self::isOn( $params, 'next_and_previous', true ),
            'show_pagination' => self::isOn( $params, 'show_pagination', false ),
            'infinite_loop' => self::isOn( $params, 'infinite_loop', true ),
            'transition' => isset( $params['transition'] ) && $params['transition'] === 'fade' ? 'fade' : 'slide',
            'autoplay' => self::isOn( $params, 'autoplay', false ),
            'autoplay_time' => isset( $params['autoplay_time'] ) && is_numeric( $params['autoplay_time'] )
                ? max( self::MIN_AUTOPLAY_TIME, min( self::MAX_AUTOPLAY_TIME, (int)$params['autoplay_time'] ) )
                : 5,
            'thumbnails_to_move' => isset( $params['thumbnails_to_move'] ) && (int)$params['thumbnails_to_move'] > 0
                ? min( 12, (int)$params['thumbnails_to_move'] )
                : 1,
            'show_details' => self::isOn( $params, 'show_details', false ),
            'show_details_on_hover' => self::isOn( $params, 'show_details_on_hover', false ),
        );
    }

    /**
     * A checkbox: $default when the block has not stored it, off for '' and '0' (the stored value of an unchecked box).
     */
    protected static function isOn( array $params, $name, $default )
    {
        if ( !isset( $params[$name] ) )
            return $default;
        $value = (string)$params[$name];
        return $value !== '' && $value !== '0';
    }

    /**
     * The gallery view type a block renders with: its stored view type when
     * that is one of the gallery view types, otherwise its definition (a
     * block stored as 'default'), otherwise 'gallery'.
     */
    public static function galleryViewType( $block )
    {
        $viewType = is_array( $block ) && isset( $block['view_type'] ) ? (string)$block['view_type'] : '';
        if ( in_array( $viewType, self::$viewTypes, true ) )
            return $viewType;

        $definition = is_array( $block ) && isset( $block['definition_identifier'] ) ? (string)$block['definition_identifier'] : '';
        if ( in_array( $definition, self::$viewTypes, true ) )
            return $definition;

        return 'gallery';
    }

    private static function buildItem( $item, $object, $imageAttribute, $thumbnailSize )
    {
        // name and node_id are what the editor preview reads from an item
        // (expLayoutsUIApplicationApi::renderBlockHtml()); without them every
        // item was skipped there and a gallery with items showed "No content".
        $entry = array(
            'name' => (string)$item->attribute( 'name' ),
            'node_id' => (int)$item->attribute( 'node_id' ),
            'alt' => $item->attribute( 'name' ),
            'link' => $item->attribute( 'url_alias' ),
            'node' => $item,
            'has_image' => false,
            'url' => '',
            'full_url' => '',
        );

        $dataMap = $object->attribute( 'data_map' );
        if ( is_array( $dataMap ) && isset( $dataMap[$imageAttribute] ) )
        {
            $attr = $dataMap[$imageAttribute];
            $image = is_object( $attr ) && $attr->hasAttribute( 'content' ) ? $attr->attribute( 'content' ) : false;
            // Only an image alias handler has aliases. An attribute of another
            // type under the same identifier (an object relation list, a
            // matrix) has an array or another object as content: calling
            // hasAttribute() on it was "Call to a member function
            // hasAttribute() on array", and the whole page failed.
            if ( is_object( $image ) && method_exists( $image, 'hasAttribute' ) && method_exists( $image, 'attribute' ) )
            {
                $url = '';
                $thumb = $image->hasAttribute( $thumbnailSize ) ? $image->attribute( $thumbnailSize ) : false;
                if ( is_array( $thumb ) && isset( $thumb['url'] ) )
                    $url = $thumb['url'];
                $fullUrl = '';
                if ( $image->hasAttribute( 'original' ) )
                {
                    $original = $image->attribute( 'original' );
                    if ( is_array( $original ) && isset( $original['url'] ) )
                        $fullUrl = (string)$original['url'];
                }
                if ( $url === '' )
                    $url = $fullUrl;
                if ( $fullUrl !== '' && strpos( $fullUrl, '/' ) !== 0 && strpos( $fullUrl, 'http' ) !== 0 )
                    $fullUrl = '/' . $fullUrl;
                if ( $url !== '' )
                {
                    // Image alias URLs are stored without a leading slash, so
                    // an <img src> built straight from one is resolved relative
                    // to the current page: on /testing/ it became
                    // /testing/var/site/storage/... and 404'd. Only a path on a
                    // one-segment URL happened to work.
                    if ( strpos( $url, '/' ) !== 0 && strpos( $url, 'http' ) !== 0 )
                        $url = '/' . $url;

                    $entry['url'] = $url;
                    $entry['full_url'] = $fullUrl !== '' ? $fullUrl : $url;
                    $entry['has_image'] = true;
                }
            }
        }

        return $entry;
    }

    private static function parseSort( $sort )
    {
        if ( is_array( $sort ) && count( $sort ) >= 2 )
            return $sort;

        $direction = false;
        if ( is_string( $sort ) )
        {
            $parts = array_map( 'trim', explode( ':', $sort ) );
            $sort = $parts[0];
            if ( isset( $parts[1] ) )
                $direction = $parts[1] === 'asc';
        }

        switch ( $sort )
        {
            case 'name':
            case 'content_name':
                return array( 'name', $direction );
            case 'modified':
            case 'date_modified':
                return array( 'modified', $direction );
            case 'priority':
                return array( 'priority', $direction );
            case 'published':
            case 'date_published':
            default:
                return array( 'published', $direction );
        }
    }
}
