<?php
class expLayoutsGalleryBlockHandler implements expLayoutsBlockHandlerInterface
{
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

        // imported nglayouts dynamic collections (queries) come first
        if ( count( $items ) === 0 && isset( $block['id'] ) )
        {
            $dynCollection = expLayoutsCollection::fetchByBlock( (int)$block['id'] );
            if ( $dynCollection && $dynCollection->attribute( 'collection_type' ) === 'dynamic' )
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

        if ( count( $items ) === 0 && isset( $block['id'] ) )
        {
            $collection = expLayoutsCollection::fetchByBlock( (int)$block['id'] );
            if ( $collection )
            {
                $collectionItems = expLayoutsCollectionItem::fetchByCollection( (int)$collection->attribute( 'id' ) );
                foreach ( $collectionItems as $collectionItem )
                {
                    $nodeId = (int)$collectionItem->attribute( 'value_id' );
                    if ( $nodeId <= 0 )
                        continue;
                    $item = eZContentObjectTreeNode::fetch( $nodeId );
                    if ( !$item )
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
        }

        return array(
            'total' => count( $items ),
            'images' => $images,
            'items' => $items,
            'thumbnail_size' => $thumbnailSize,
        );
    }

    private static function buildItem( $item, $object, $imageAttribute, $thumbnailSize )
    {
        $entry = array(
            'alt' => $item->attribute( 'name' ),
            'link' => $item->attribute( 'url_alias' ),
            'node' => $item,
            'has_image' => false,
            'url' => '',
        );

        $dataMap = $object->attribute( 'data_map' );
        if ( isset( $dataMap[$imageAttribute] ) )
        {
            $attr = $dataMap[$imageAttribute];
            $image = $attr->hasAttribute( 'content' ) ? $attr->attribute( 'content' ) : false;
            if ( $image )
            {
                $url = '';
                $thumb = $image->hasAttribute( $thumbnailSize ) ? $image->attribute( $thumbnailSize ) : false;
                if ( is_array( $thumb ) && isset( $thumb['url'] ) )
                    $url = $thumb['url'];
                if ( $url === '' && $image->hasAttribute( 'original' ) )
                {
                    $original = $image->attribute( 'original' );
                    if ( is_array( $original ) && isset( $original['url'] ) )
                        $url = $original['url'];
                }
                if ( $url !== '' )
                {
                    $entry['url'] = $url;
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
