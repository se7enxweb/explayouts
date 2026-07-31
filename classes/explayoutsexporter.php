<?php
class expLayoutsExponentialorter
{
    static function exportLayout( $layoutId )
    {
        return json_encode( self::exportArray( $layoutId ), JSON_PRETTY_PRINT );
    }

    static function exportArray( $layoutId )
    {
        $layout = expLayoutsLayout::fetch( $layoutId, 2 );
        if ( !$layout )
            return false;

        $zones = array();
        foreach ( expLayoutsZone::fetchByLayout( $layoutId, 2 ) as $zone )
        {
            $blocks = array();
            foreach ( expLayoutsBlock::fetchByZone( $zone->attribute( 'id' ), 2 ) as $block )
            {
                $blocks[] = self::exportBlock( $block );
            }
            $zones[] = array(
                'id' => (int)$zone->attribute( 'id' ),
                'identifier' => $zone->attribute( 'identifier' ),
                'position' => (int)$zone->attribute( 'position' ),
                'linked_layout_id' => $zone->attribute( 'linked_layout_id' ),
                'blocks' => $blocks,
            );
        }

        return array(
            'version' => 1,
            'identifier' => $layout->attribute( 'identifier' ),
            'name' => $layout->attribute( 'name' ),
            'layout_type' => $layout->attribute( 'layout_type' ),
            'zones' => $zones,
        );
    }

    static function exportBlock( $block )
    {
        $parameters = array();
        foreach ( expLayoutsBlockParameter::fetchByBlock( $block->attribute( 'id' ) ) as $param )
        {
            $parameters[$param->attribute( 'name' )] = $param->attribute( 'value' );
        }

        $collection = expLayoutsCollection::fetchByBlock( $block->attribute( 'id' ) );
        $exportedCollection = false;
        if ( $collection )
        {
            $items = array();
            foreach ( expLayoutsCollectionItem::fetchByCollection( $collection->attribute( 'id' ) ) as $item )
            {
                $items[] = array(
                    'position' => (int)$item->attribute( 'position' ),
                    'value_type' => $item->attribute( 'value_type' ),
                    'value_id' => (int)$item->attribute( 'value_id' ),
                    'item_type' => $item->attribute( 'item_type' ),
                );
            }

            $exportedCollection = array(
                'collection_type' => $collection->attribute( 'collection_type' ),
                'offset_value' => (int)$collection->attribute( 'offset_value' ),
                'limit_value' => (int)$collection->attribute( 'limit_value' ),
                'status' => (int)$collection->attribute( 'status' ),
                'items' => $items,
            );
        }

        return array(
            'id' => (int)$block->attribute( 'id' ),
            'definition_identifier' => $block->attribute( 'definition_identifier' ),
            'view_type' => $block->attribute( 'view_type' ),
            'name' => $block->attribute( 'name' ),
            'position' => (int)$block->attribute( 'position' ),
            'parameters' => $parameters,
            'collection' => $exportedCollection,
        );
    }
}
