<?php
class expLayoutsRenderer
{
    static function prepareLayout( $layout, $status = 2 )
    {
        if ( !$layout instanceof expLayoutsLayout )
            return false;

        $zones = expLayoutsZone::fetchByLayout( $layout->attribute( 'id' ), $status );
        $preparedZones = array();
        $blockCount = 0;
        foreach ( $zones as $zone )
        {
            $preparedZones[] = self::prepareZone( $zone, $status );
        }

        foreach ( $preparedZones as $preparedZone )
        {
            $blockCount += count( $preparedZone['blocks'] );
        }

        return array(
            'id' => $layout->attribute( 'id' ),
            'identifier' => $layout->attribute( 'identifier' ),
            'name' => $layout->attribute( 'name' ),
            'layout_type' => $layout->attribute( 'layout_type' ),
            'zones' => $preparedZones,
            'block_count' => $blockCount,
        );
    }

    static function prepareZone( $zone, $status = 2 )
    {
        // Linked zones (nglayouts zone linking): header/footer link to the
        // shared "Header / Footer" layout, pre_footer links to "Prefooter".
        // Render the linked layout's zone blocks instead of our own.
        $sourceZone = $zone;
        $linkedLayoutId = (int)$zone->attribute( 'linked_layout_id' );
        if ( $linkedLayoutId > 0 )
        {
            $targetZone = false;
            foreach ( expLayoutsZone::fetchByLayout( $linkedLayoutId, $status ) as $candidate )
            {
                if ( $candidate->attribute( 'identifier' ) === $zone->attribute( 'identifier' ) )
                {
                    $targetZone = $candidate;
                    break;
                }
                if ( $candidate->attribute( 'identifier' ) === 'main' )
                    $targetZone = $candidate;
            }
            if ( $targetZone )
                $sourceZone = $targetZone;
        }

        $blocks = expLayoutsBlock::fetchByZone( $sourceZone->attribute( 'id' ), $status );
        $preparedBlocks = array();
        $blocksById = array();
        foreach ( $blocks as $block )
        {
            $preparedBlock = self::prepareBlock( $block );
            $preparedBlock['parent_id'] = (int)$block->attribute( 'parent_id' );
            $preparedBlock['placeholder'] = $block->attribute( 'placeholder' );
            $preparedBlock['children'] = array();
            $preparedBlocks[] = $preparedBlock;
            $blocksById[$preparedBlock['id']] = &$preparedBlocks[count( $preparedBlocks ) - 1];
        }
        unset( $preparedBlock );

        foreach ( $preparedBlocks as $key => $preparedBlock )
        {
            if ( $preparedBlock['parent_id'] > 0 && isset( $blocksById[$preparedBlock['parent_id']] ) )
            {
                $preparedBlocks[$key]['parent_id'] = $preparedBlock['parent_id'];
                $blocksById[$preparedBlock['parent_id']]['children'][] = $preparedBlocks[$key];
            }
        }

        return array(
            'id' => $zone->attribute( 'id' ),
            'identifier' => $zone->attribute( 'identifier' ),
            'blocks' => $preparedBlocks,
            'items' => self::buildZoneItems( $preparedBlocks ),
        );
    }

    static function buildZoneItems( $preparedBlocks )
    {
        // Some imported two_columns groupings are broken; only nest the ones we trust.
        $flatIds = array( 398, 406 );

        $items = array();
        $i = 0;
        $n = count( $preparedBlocks );
        while ( $i < $n )
        {
            $block = $preparedBlocks[$i];
            if ( $block['definition_identifier'] == 'two_columns' && !in_array( $block['id'], $flatIds ) )
            {
                $start = $i;
                $columns = array( array(), array() );
                $currentCol = 0;
                $i++;
                while ( $i < $n && $preparedBlocks[$i]['definition_identifier'] != 'two_columns' )
                {
                    $next = $preparedBlocks[$i];
                    if ( $next['definition_identifier'] == 'column' )
                    {
                        if ( $currentCol === 1 )
                        {
                            // stray/ending column marker; stop consuming
                            $i++;
                            break;
                        }
                        $currentCol = 1;
                        $i++;
                        continue;
                    }
                    $columns[$currentCol][] = $next;
                    $i++;
                }
                $preparedBlocks[$start]['values']['columns'] = $columns;
                $items[] = array(
                    'type' => 'block',
                    'block' => $preparedBlocks[$start],
                );
                continue;
            }
            elseif ( $block['definition_identifier'] == 'column' || $block['definition_identifier'] == 'two_columns' )
            {
                $i++;
                continue;
            }

            $items[] = array(
                'type' => 'block',
                'block' => $block,
            );
            $i++;
        }
        return $items;
    }

    static function resolveBlockLink( $value )
    {
        if ( !is_string( $value ) || $value === '' )
            return $value;

        // Plain ibexa-location URI (legacy / pre-JSON values)
        if ( strpos( $value, 'ibexa-location://' ) === 0 )
        {
            $nexusId = (int)substr( $value, 17 );
            $nodeId = expLayoutsDynamicCollection::remapNodeId( $nexusId );
            $node = $nodeId ? eZContentObjectTreeNode::fetch( $nodeId, false, true ) : false;
            if ( $node )
                return $node->attribute( 'url_alias' );
            return $value;
        }

        if ( $value[0] !== '{' )
            return $value;

        $data = json_decode( $value, true );
        if ( !is_array( $data ) || empty( $data['link_type'] ) )
            return $value;

        $link = isset( $data['link'] ) ? $data['link'] : '';
        if ( $data['link_type'] === 'internal' && strpos( $link, 'ibexa-location://' ) === 0 )
        {
            $nexusId = (int)substr( $link, 17 );
            $nodeId = expLayoutsDynamicCollection::remapNodeId( $nexusId );
            $node = $nodeId ? eZContentObjectTreeNode::fetch( $nodeId, false, true ) : false;
            if ( $node )
                return $node->attribute( 'url_alias' );
        }
        elseif ( $data['link_type'] === 'internal' && strpos( $link, 'ibexa-object://' ) === 0 )
        {
            $objectId = (int)substr( $link, 15 );
            $object = eZContentObject::fetch( $objectId );
            $node = $object ? $object->attribute( 'main_node' ) : false;
            if ( $node )
                return $node->attribute( 'url_alias' );
        }
        elseif ( $data['link_type'] === 'url' )
        {
            return $link;
        }

        return $value;
    }

    static function prepareBlock( $block )
    {
        $params = array();
        foreach ( expLayoutsBlockParameter::fetchByBlock( $block->attribute( 'id' ) ) as $param )
        {
            $params[$param->attribute( 'name' )] = $param->attribute( 'value' );
        }

        if ( isset( $params['link'] ) )
        {
            $params['link'] = self::resolveBlockLink( $params['link'] );
        }

        $blockArray = array(
            'id' => $block->attribute( 'id' ),
            'position' => (int)$block->attribute( 'position' ),
            'definition_identifier' => $block->attribute( 'definition_identifier' ),
            'view_type' => $block->attribute( 'view_type' ),
            // How each ITEM inside this block renders (overlay, listitem, line, mini,
            // standard_with_intro, standard) -- the reference drives item markup off
            // this, so it has to reach the templates alongside view_type.
            'item_view_type' => $block->attribute( 'item_view_type' ),
            'name' => $block->attribute( 'name' ),
            'parameters' => $params,
        );
        $handler = expLayoutsBlockHandlerFactory::get( $blockArray['definition_identifier'] );
        if ( $handler )
        {
            $blockArray['values'] = $handler->getValues( $blockArray );
        }
        else
        {
            $blockArray['values'] = array();
        }

        return $blockArray;
    }
}
