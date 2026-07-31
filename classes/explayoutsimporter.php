<?php
class expLayoutsImporter
{
    static function importJson( $json )
    {
        $data = json_decode( $json, true );
        if ( !is_array( $data ) )
            return array( 'error' => 'Invalid JSON provided.' );

        return self::import( $data );
    }

    static function import( array $data )
    {
        if ( !isset( $data['version'] ) || (int)$data['version'] !== 1 )
            return array( 'error' => 'Unsupported export version. Only version 1 is supported.' );

        $required = array( 'identifier', 'name', 'layout_type', 'zones' );
        foreach ( $required as $key )
        {
            if ( !isset( $data[$key] ) )
                return array( 'error' => 'Missing required field: ' . $key );
        }

        $baseIdentifier = preg_replace( '/[^a-zA-Z0-9_-]/', '_', $data['identifier'] );
        $uniqueIdentifier = 'import_' . time() . '_' . $baseIdentifier;

        $layout = expLayoutsLayout::create( $uniqueIdentifier );
        if ( !$layout )
            return array( 'error' => 'Could not create layout.' );

        $layout->setAttribute( 'name', $data['name'] . ' (imported)' );
        $layout->setAttribute( 'layout_type', $data['layout_type'] );
        $layout->setAttribute( 'status', 1 );
        $layout->setAttribute( 'created', time() );
        $layout->setAttribute( 'modified', time() );
        $layout->store();

        $layoutId = (int)$layout->attribute( 'id' );
        $zoneMap = array();

        foreach ( $data['zones'] as $zoneData )
        {
            $zone = expLayoutsZone::create( $layoutId, $zoneData['identifier'], 1 );
            if ( isset( $zoneData['position'] ) )
                $zone->setAttribute( 'position', (int)$zoneData['position'] );
            if ( isset( $zoneData['linked_layout_id'] ) )
                $zone->setAttribute( 'linked_layout_id', (int)$zoneData['linked_layout_id'] );
            $zone->store();

            $oldZoneId = isset( $zoneData['id'] ) ? (int)$zoneData['id'] : 0;
            if ( $oldZoneId > 0 )
                $zoneMap[$oldZoneId] = (int)$zone->attribute( 'id' );

            if ( isset( $zoneData['blocks'] ) && is_array( $zoneData['blocks'] ) )
            {
                foreach ( $zoneData['blocks'] as $blockData )
                {
                    self::importBlock( $blockData, $zone->attribute( 'id' ), $layoutId );
                }
            }
        }

        return array( 'layout_id' => $layoutId, 'layout' => $layout );
    }

    static function importBlock( array $blockData, $zoneId, $layoutId )
    {
        $name = isset( $blockData['name'] ) ? $blockData['name'] : ( isset( $blockData['definition_identifier'] ) ? $blockData['definition_identifier'] : 'block' );
        $definition = isset( $blockData['definition_identifier'] ) ? $blockData['definition_identifier'] : 'text';

        $block = expLayoutsBlock::create( $zoneId, $layoutId, $definition, $name );
        if ( isset( $blockData['view_type'] ) )
            $block->setAttribute( 'view_type', $blockData['view_type'] );
        if ( isset( $blockData['position'] ) )
            $block->setAttribute( 'position', (int)$blockData['position'] );
        $block->store();

        $blockId = (int)$block->attribute( 'id' );

        if ( isset( $blockData['parameters'] ) && is_array( $blockData['parameters'] ) )
        {
            foreach ( $blockData['parameters'] as $paramName => $paramValue )
            {
                expLayoutsBlockParameter::set( $blockId, $paramName, $paramValue );
            }
        }

        if ( isset( $blockData['collection'] ) && is_array( $blockData['collection'] ) )
        {
            $collectionData = $blockData['collection'];
            $collection = expLayoutsCollection::create( $blockId, isset( $collectionData['collection_type'] ) ? $collectionData['collection_type'] : 'manual' );
            if ( isset( $collectionData['offset_value'] ) )
                $collection->setAttribute( 'offset_value', (int)$collectionData['offset_value'] );
            if ( isset( $collectionData['limit_value'] ) )
                $collection->setAttribute( 'limit_value', (int)$collectionData['limit_value'] );
            if ( isset( $collectionData['status'] ) )
                $collection->setAttribute( 'status', (int)$collectionData['status'] );
            $collection->store();

            $collectionId = (int)$collection->attribute( 'id' );
            if ( isset( $collectionData['items'] ) && is_array( $collectionData['items'] ) )
            {
                foreach ( $collectionData['items'] as $itemData )
                {
                    $item = expLayoutsCollectionItem::create(
                        $collectionId,
                        isset( $itemData['value_id'] ) ? (int)$itemData['value_id'] : 0,
                        isset( $itemData['value_type'] ) ? $itemData['value_type'] : 'ez_content',
                        isset( $itemData['item_type'] ) ? $itemData['item_type'] : 'manual'
                    );
                    if ( isset( $itemData['position'] ) )
                        $item->setAttribute( 'position', (int)$itemData['position'] );
                    $item->store();
                }
            }
        }

        return $block;
    }
}
