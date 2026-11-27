<?php
class expLayoutsRenderer
{
    /** The layouts prepareLayout() prepared in this request, id => prepared, for the debug bar's panel (prepared()) */
    private static $prepared = array();

    /** The request $prepared belongs to ($_SERVER['REQUEST_TIME_FLOAT']), null before the first one */
    private static $preparedRequest = null;

    /**
     * Forgets the layouts an earlier request prepared: a persistent worker (Exponential Velocity with
     * ForkPerRequest=disabled) keeps class statics from one request to the next. See
     * expLayoutsResolver::forgetEarlierRequests().
     */
    static function forgetEarlierRequests()
    {
        $request = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : null;
        if ( $request === self::$preparedRequest )
            return;
        if ( self::$preparedRequest !== null )
            self::$prepared = array();
        self::$preparedRequest = $request;
    }

    /**
     * The layout $layoutId as prepareLayout() prepared it in this request, or null when it was not prepared. Read by
     * expLayoutsDebugBarPanel, nothing else depends on it.
     *
     * @param int $layoutId
     * @return array|null
     */
    static function prepared( $layoutId )
    {
        self::forgetEarlierRequests();
        return isset( self::$prepared[(int)$layoutId] ) ? self::$prepared[(int)$layoutId] : null;
    }

    /**
     * The layout with its zones and blocks, ready for design:explayouts/layout.tpl.
     *
     * With $withValues false the blocks carry their parameters but no values: the block handlers (collection
     * queries, node loads, link lookups) do not run, and 'values' is an empty array. That is what a page needs
     * to decide whether it has a layout and to build the key of the cache-block that holds the rendered layout;
     * the values are only needed when that cache-block is generated. A layout prepared without values is kept
     * for the debug bar only when no full one was prepared in this request.
     *
     * @param expLayoutsLayout $layout
     * @param int $status
     * @param bool $withValues
     * @return array|false
     */
    static function prepareLayout( $layout, $status = 2, $withValues = true )
    {
        if ( !$layout instanceof expLayoutsLayout )
            return false;

        $zones = expLayoutsZone::fetchByLayout( $layout->attribute( 'id' ), $status );
        $preparedZones = array();
        $blockCount = 0;
        foreach ( $zones as $zone )
        {
            $preparedZones[] = self::prepareZone( $zone, $status, $withValues );
        }

        foreach ( $preparedZones as $preparedZone )
        {
            $blockCount += count( $preparedZone['blocks'] );
        }

        $prepared = array(
            'id' => $layout->attribute( 'id' ),
            'identifier' => $layout->attribute( 'identifier' ),
            'name' => $layout->attribute( 'name' ),
            'layout_type' => $layout->attribute( 'layout_type' ),
            'zones' => $preparedZones,
            'block_count' => $blockCount,
        );
        self::forgetEarlierRequests();
        if ( $withValues || !isset( self::$prepared[(int)$prepared['id']] ) )
            self::$prepared[(int)$prepared['id']] = $prepared;
        return $prepared;
    }

    /**
     * What a layout is made of, without what it shows: the same id, identifier,
     * name, layout_type, zones and block_count as prepareLayout(), where each
     * zone carries its identifier, link and block_count only. One query for the
     * zones and one count per zone, instead of every block with its parameters,
     * collections and items, so a page that only names the layout (the Layouts
     * tab of the admin node view) does not load what a visitor sees.
     */
    static function summarizeLayout( $layout, $status = 2 )
    {
        if ( !$layout instanceof expLayoutsLayout )
            return false;

        $zones = array();
        $blockCount = 0;
        foreach ( expLayoutsZone::fetchByLayout( $layout->attribute( 'id' ), $status ) as $zone )
        {
            $sourceZone = expLayoutsZone::resolveSource( $zone );
            $blockStatus = $sourceZone === $zone ? $status : (int)$sourceZone->attribute( 'status' );
            $conditions = array( 'zone_id' => (int)$sourceZone->attribute( 'id' ) );
            if ( $blockStatus !== null )
                $conditions['status'] = $blockStatus;
            $count = (int)eZPersistentObject::count( expLayoutsBlock::definition(), $conditions );
            $blockCount += $count;
            $zones[] = array(
                'id' => $zone->attribute( 'id' ),
                'identifier' => $zone->attribute( 'identifier' ),
                'linked_layout_id' => $zone->isLinked() ? (int)$zone->attribute( 'linked_layout_id' ) : null,
                'linked_zone_identifier' => $zone->isLinked() ? (string)$zone->attribute( 'linked_zone_identifier' ) : null,
                'block_count' => $count,
            );
        }

        return array(
            'id' => $layout->attribute( 'id' ),
            'identifier' => $layout->attribute( 'identifier' ),
            'name' => $layout->attribute( 'name' ),
            'layout_type' => $layout->attribute( 'layout_type' ),
            'zones' => $zones,
            'block_count' => $blockCount,
        );
    }

    static function prepareZone( $zone, $status = 2, $withValues = true )
    {
        // Linked zones (zone linking): header/footer link to the shared
        // "Header / Footer" layout, pre_footer links to "Prefooter". Render
        // the linked layout's zone blocks instead of our own. The link names
        // its target zone, so nothing here has to guess which one it is.
        $sourceZone = expLayoutsZone::resolveSource( $zone );
        $blockStatus = $sourceZone === $zone ? $status : (int)$sourceZone->attribute( 'status' );

        $blocks = expLayoutsBlock::fetchByZone( $sourceZone->attribute( 'id' ), $blockStatus );
        $preparedBlocks = array();
        $blocksById = array();
        // The parameters of every block of the zone in one query
        $blockIds = array();
        foreach ( $blocks as $block )
            $blockIds[] = (int)$block->attribute( 'id' );
        $parameters = expLayoutsBlockParameter::fetchValuesByBlocks( $blockIds );
        foreach ( $blocks as $block )
        {
            $preparedBlock = self::prepareBlock( $block, $withValues, $parameters[(int)$block->attribute( 'id' )] );
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
            'linked_layout_id' => $zone->isLinked() ? (int)$zone->attribute( 'linked_layout_id' ) : null,
            'linked_zone_identifier' => $zone->isLinked() ? (string)$zone->attribute( 'linked_zone_identifier' ) : null,
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

        // A plain location reference (pre-JSON values): exp-remote-location://,
        // ezlocation:// or the deprecated ibexa-location:// of old data, see
        // expLayoutsLinkParameter::referencedNode()
        if ( expLayoutsLinkParameter::isLocationReference( $value ) )
        {
            $node = expLayoutsLinkParameter::referencedNode( $value );
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
        if ( $data['link_type'] === 'internal' && expLayoutsLinkParameter::isLocationReference( $link ) )
        {
            $node = expLayoutsLinkParameter::referencedNode( $link );
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

    /**
     * @param expLayoutsBlock $block
     * @param bool $withValues false: no handler values and no link lookup (see prepareLayout())
     * @param array|null $params the block's parameters (name => value) when the caller read them already
     *                           (expLayoutsBlockParameter::fetchValuesByBlocks()), null reads them here
     * @return array
     */
    static function prepareBlock( $block, $withValues = true, $params = null )
    {
        if ( !is_array( $params ) )
        {
            $params = array();
            foreach ( expLayoutsBlockParameter::fetchByBlock( $block->attribute( 'id' ) ) as $param )
            {
                $params[$param->attribute( 'name' )] = $param->attribute( 'value' );
            }
        }

        if ( $withValues && isset( $params['link'] ) )
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
        $handler = $withValues ? expLayoutsBlockHandlerFactory::get( $blockArray['definition_identifier'] ) : null;
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
