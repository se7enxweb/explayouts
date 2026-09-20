<?php
/**
 * Executes 'dynamic' collections (imported nglayouts collection queries).
 *
 * Supported query types:
 *  - exponential_content_search: subtree fetch with content-type filter, publish-date
 *    sort, only-main-locations and exclude-current-location semantics.
 *  - content_by_topic: alpha has no eztags data, so these collections are
 *    materialized as manual items by the repair tooling; when manual items
 *    exist they are returned, otherwise the query resolves empty.
 *
 * parent_location_id values in imported query parameters are nexus location
 * ids; the nexus->alpha node offset is +554 (see ai memory nexus-alpha-id-offsets).
 */
class expLayoutsDynamicCollection
{
    const NODE_OFFSET = 554;

    /**
     * @return array|false array('total' =>, 'items' =>) or false when the
     *                     collection cannot be executed dynamically.
     */
    static function fetch( $collection )
    {
        $collectionId = (int)$collection->attribute( 'id' );
        $query = self::queryRow( $collectionId );
        if ( !$query )
            return false;

        $params = json_decode( (string)$query['parameters'], true );
        if ( !is_array( $params ) )
            $params = array();

        $offset = (int)$collection->attribute( 'offset_value' );
        $limit = (int)$collection->attribute( 'limit_value' );
        if ( $limit <= 0 && isset( $params['limit'] ) && (int)$params['limit'] > 0 )
            $limit = (int)$params['limit'];

        switch ( $query['query_type'] )
        {
            case 'exponential_content_search':
                $result = self::contentSearch( $params, $offset, $limit );
                break;
            case 'content_by_topic':
            {
                $result = self::contentByTopic( $params, $offset, $limit );
                if ( $result === false || $result['total'] === 0 )
                {
                    // fall back to materialized items; those are already the
                    // final list, so skip pinned-item merging below
                    return self::manualItems( $collectionId, $offset, $limit );
                }
                break;
            }
            default:
                return false;
        }

        if ( $result !== false && $offset === 0 )
            $result = self::applyPinnedItems( $collectionId, $result, $limit, $offset );
        return $result;
    }

    /**
     * Manual items stored on a dynamic collection are pinned overrides: they
     * occupy their stored position, query results fill the slots around them.
     * Pinned positions are absolute within the full collection; only those
     * falling inside the current [offset, offset+limit) window are rendered.
     */
    /**
     * Fetch several nodes in one query, in the order asked for.
     *
     * Fetching them one at a time costs a round trip each, and a page listing
     * fifty items paid fifty of them. eZContentObjectTreeNode::fetch()
     * accepts an array on both engines; the result comes back in storage
     * order, so it is reordered here to match the caller's list. Missing or
     * unreadable nodes are dropped, exactly as the per-item loops did.
     */
    static function fetchNodesInOrder( array $nodeIds )
    {
        $wanted = array();
        foreach ( $nodeIds as $nodeId )
        {
            $nodeId = (int) $nodeId;
            if ( $nodeId > 0 )
                $wanted[] = $nodeId;
        }
        if ( !$wanted )
            return array();

        $fetched = eZContentObjectTreeNode::fetch( array_values( array_unique( $wanted ) ) );
        if ( $fetched instanceof eZContentObjectTreeNode )
            $fetched = array( $fetched );
        if ( !is_array( $fetched ) )
            return array();

        $byId = array();
        foreach ( $fetched as $node )
        {
            if ( $node instanceof eZContentObjectTreeNode )
                $byId[(int) $node->attribute( 'node_id' )] = $node;
        }

        // The caller's order is the collection's order, and a node listed
        // twice is rendered twice, so this walks the original list.
        $ordered = array();
        foreach ( $wanted as $nodeId )
        {
            if ( isset( $byId[$nodeId] ) )
                $ordered[] = $byId[$nodeId];
        }

        return $ordered;
    }

    static function applyPinnedItems( $collectionId, $result, $limit = 0, $offset = 0 )
    {
        $pinnedItems = expLayoutsCollectionItem::fetchByCollection( $collectionId, true );
        $pinnedIdList = array();
        foreach ( $pinnedItems as $item )
            $pinnedIdList[] = (int) $item->attribute( 'value_id' );

        $pinnedNodes = array();
        foreach ( self::fetchNodesInOrder( $pinnedIdList ) as $node )
            $pinnedNodes[(int) $node->attribute( 'node_id' )] = $node;

        $pinned = array();
        foreach ( $pinnedItems as $item )
        {
            $valueId = (int) $item->attribute( 'value_id' );
            if ( isset( $pinnedNodes[$valueId] ) )
                $pinned[(int) $item->attribute( 'position' )] = $pinnedNodes[$valueId];
        }
        if ( empty( $pinned ) )
            return $result;

        $queryItems = $result['items'];
        $pinnedIds = array();
        foreach ( $pinned as $node )
            $pinnedIds[] = (int)$node->attribute( 'node_id' );

        $windowSize = $limit > 0 ? $limit : ( count( $queryItems ) + count( $pinned ) );
        $merged = array();

        // Place pinned items that belong to the current page window.
        foreach ( $pinned as $pos => $node )
        {
            $relPos = $pos - $offset;
            if ( $relPos >= 0 && $relPos < $windowSize )
                $merged[$relPos] = $node;
        }

        // Fill remaining slots with query results, skipping pinned ids.
        $qi = 0;
        for ( $pos = 0; $pos < $windowSize; $pos++ )
        {
            if ( isset( $merged[$pos] ) )
                continue;
            while ( $qi < count( $queryItems ) && in_array( (int)$queryItems[$qi]->attribute( 'node_id' ), $pinnedIds ) )
                $qi++;
            if ( $qi < count( $queryItems ) )
            {
                $merged[$pos] = $queryItems[$qi];
                $qi++;
            }
        }

        ksort( $merged );
        return array( 'total' => $result['total'], 'items' => array_values( $merged ) );
    }

    static function queryRow( $collectionId )
    {
        $query = expLayoutsCollectionQuery::fetchByCollection( (int)$collectionId, true );
        if ( $query )
        {
            return array(
                'query_type' => $query->attribute( 'query_type' ),
                'parameters' => $query->attribute( 'parameters' ),
            );
        }
        return false;
    }

    static function manualItems( $collectionId, $offset = 0, $limit = 0 )
    {
        $items = expLayoutsCollectionItem::fetchByCollection( $collectionId, true );
        $valueIds = array();
        foreach ( $items as $item )
            $valueIds[] = (int) $item->attribute( 'value_id' );

        $nodes = self::fetchNodesInOrder( $valueIds );
        $total = count( $nodes );
        if ( $offset > 0 || $limit > 0 )
            $nodes = array_slice( $nodes, $offset, $limit > 0 ? $limit : null );
        return array( 'total' => $total, 'items' => $nodes );
    }

    static function remapNodeId( $nexusId )
    {
        $nexusId = (int)$nexusId;
        if ( $nexusId <= 0 )
            return 0;
        $ini = eZINI::instance( 'explayouts.ini' );
        $key = (string)$nexusId;
        if ( $ini->hasVariable( 'NexusNodeMap', $key ) )
        {
            $mapped = trim( (string)$ini->variable( 'NexusNodeMap', $key ) );

            // A non-numeric value is a remote id. Node ids are handed out at
            // install time and are not stable between installations, so a map
            // written in node ids silently starts pointing at whatever content
            // happens to hold that id next time - which is how the "All
            // Recipes" button came to link at a test component. Remote ids are
            // assigned by the content package and do not move, so they are the
            // form to use for anything shipped.
            if ( $mapped !== '' && !ctype_digit( $mapped ) )
            {
                $node = eZContentObjectTreeNode::fetchByRemoteID( $mapped );
                if ( $node instanceof eZContentObjectTreeNode )
                    return (int)$node->attribute( 'node_id' );

                eZDebug::writeWarning( "NexusNodeMap entry $key points at remote id" .
                                       " '$mapped', which no node carries", __METHOD__ );
                return 0;
            }

            $mapped = (int)$mapped;
            if ( eZContentObjectTreeNode::fetch( $mapped, false, false ) )
                return $mapped;
            return 0;
        }
        // Fallback: when the nexus parent location id already exists in alpha,
        // treat it as an identity mapping. This covers imported top-level section
        // nodes (Video, Running, etc.) while the explicit [NexusNodeMap] builds.
        if ( eZContentObjectTreeNode::fetch( $nexusId, false, false ) )
            return $nexusId;
        return 0;
    }

    static function contentSearch( array $params, $offset = 0, $limit = 0 )
    {
        $parentNodeId = self::remapNodeId( isset( $params['parent_location_id'] ) ? $params['parent_location_id'] : 0 );
        if ( !empty( $params['use_current_location'] ) )
        {
            $current = self::currentNode();
            if ( $current )
                $parentNodeId = (int)$current->attribute( 'node_id' );
        }
        if ( $parentNodeId <= 0 )
            return array( 'total' => 0, 'items' => array() );

        $sortField = 'published';
        if ( isset( $params['sort_type'] ) && $params['sort_type'] === 'date_modified' )
            $sortField = 'modified';
        elseif ( isset( $params['sort_type'] ) && $params['sort_type'] === 'content_name' )
            $sortField = 'name';
        elseif ( isset( $params['sort_type'] ) && $params['sort_type'] === 'location_priority' )
            // The reference's location_priority sorts on the location's own
            // priority field, which is how editors order children by hand.
            // Without this it silently fell back to publication date, so any
            // hand-ordered collection came out in the wrong order.
            $sortField = 'priority';
        $sortAsc = ( isset( $params['sort_direction'] ) && strtolower( (string)$params['sort_direction'] ) === 'ascending' );

        $filterBySection = !empty( $params['filter_by_section'] ) && !empty( $params['sections'] );
        $filterByObjectState = !empty( $params['filter_by_object_state'] ) && !empty( $params['object_states'] );
        $hasExtraFilters = $filterBySection || $filterByObjectState;

        $fetchParams = array(
            'SortBy' => array( array( $sortField, $sortAsc ) ),
            'MainNodeOnly' => !isset( $params['only_main_locations'] ) || $params['only_main_locations'],
            'IgnoreVisibility' => false,
        );

        if ( !isset( $params['query_type'] ) || $params['query_type'] !== 'tree' )
        {
            $fetchParams['Depth'] = 1;
            $fetchParams['DepthOperator'] = 'eq';
        }

        if ( !empty( $params['filter_by_content_type'] ) && !empty( $params['content_types'] ) )
        {
            $types = array_values( (array)$params['content_types'] );
            $fetchParams['ClassFilterType'] = ( isset( $params['content_types_filter'] ) && $params['content_types_filter'] === 'exclude' ) ? 'exclude' : 'include';
            $fetchParams['ClassFilterArray'] = $types;
        }

        $excludeNodeId = 0;
        if ( !empty( $params['exclude_current_location'] ) )
        {
            $current = self::currentNode();
            if ( $current )
                $excludeNodeId = (int)$current->attribute( 'node_id' );
        }

        // When filtering by section or object state we must fetch the whole
        // candidate set and apply the filters in PHP (eZ's subtree API does not
        // support those dimensions directly).
        if ( $hasExtraFilters )
        {
            $fetchParams['Limit'] = false;
            $fetchParams['Offset'] = false;
        }
        else
        {
            // over-fetch a little so exclude+offset+limit still fill the page
            $fetchParams['Limit'] = ( $limit > 0 ? $limit : 50 ) + $offset + ( $excludeNodeId ? 1 : 0 );
            $fetchParams['Offset'] = 0;
        }

        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $fetchParams, $parentNodeId );
        if ( !is_array( $nodes ) )
            $nodes = array();

        if ( $excludeNodeId )
        {
            $filtered = array();
            foreach ( $nodes as $node )
            {
                if ( (int)$node->attribute( 'node_id' ) !== $excludeNodeId )
                    $filtered[] = $node;
            }
            $nodes = $filtered;
        }

        if ( $filterBySection || $filterByObjectState )
        {
            $sectionIds = $filterBySection ? self::resolveSectionIds( $params['sections'] ) : array();
            $stateIds = $filterByObjectState ? self::resolveObjectStateIds( $params['object_states'] ) : array();

            $filtered = array();
            foreach ( $nodes as $node )
            {
                if ( $filterBySection && !in_array( (int)$node->attribute( 'section_id' ), $sectionIds, true ) )
                    continue;

                if ( $filterByObjectState )
                {
                    $object = $node->object();
                    if ( !$object )
                        continue;
                    $nodeStateIds = array_map( 'intval', (array)$object->attribute( 'state_id_array' ) );
                    if ( count( array_intersect( $nodeStateIds, $stateIds ) ) === 0 )
                        continue;
                }

                $filtered[] = $node;
            }
            $nodes = $filtered;
        }

        $total = eZContentObjectTreeNode::subTreeCountByNodeID( $fetchParams, $parentNodeId );
        if ( $total === null )
            $total = 0;

        if ( $hasExtraFilters )
        {
            $total = count( $nodes );
            $nodes = array_slice( $nodes, $offset, $limit > 0 ? $limit : null );
        }
        else
        {
            $nodes = array_slice( $nodes, $offset, $limit > 0 ? $limit : null );
        }

        return array( 'total' => $total, 'items' => $nodes );
    }

    /**
     * Map section identifiers (e.g. 'standard', 'media') to their numeric IDs.
     */
    static function resolveSectionIds( $sectionIdentifiers )
    {
        static $map = null;
        if ( $map === null )
        {
            $map = array();
            foreach ( eZSection::fetchList() as $section )
            {
                $map[(string)$section->attribute( 'identifier' )] = (int)$section->attribute( 'id' );
            }
        }

        $ids = array();
        foreach ( array_values( (array)$sectionIdentifiers ) as $ident )
        {
            if ( isset( $map[(string)$ident] ) )
                $ids[] = $map[(string)$ident];
        }
        return array_unique( $ids );
    }

    /**
     * The object states, as group and state identifiers with their IDs,
     * ordered by group identifier and then state priority.
     *
     * MongoDB has no JOIN and the driver refuses SQL it cannot translate
     * rather than guessing at it, so the two collections are read separately
     * and matched here. Both callers used to receive an empty list on
     * MongoDB - silently, since an empty result is indistinguishable from a
     * site with no states configured.
     */
    static function fetchObjectStateRows()
    {
        $db = eZDB::instance();

        if ( $db->databaseName() === 'mongo' )
        {
            $groups = array();
            foreach ( $db->arrayQuery( 'SELECT id, identifier FROM ezcobj_state_group' ) as $group )
                $groups[(int)$group['id']] = (string)$group['identifier'];

            $rows = array();
            foreach ( $db->arrayQuery( 'SELECT id, group_id, identifier, priority FROM ezcobj_state' ) as $state )
            {
                $groupId = (int)$state['group_id'];
                // An INNER JOIN drops a state whose group has gone.
                if ( !isset( $groups[$groupId] ) )
                    continue;

                $rows[] = array(
                    'group_identifier' => $groups[$groupId],
                    'state_identifier' => (string)$state['identifier'],
                    'id' => (int)$state['id'],
                    'priority' => (int)$state['priority'],
                );
            }

            usort( $rows, function ( $first, $second )
            {
                $byGroup = strcmp( $first['group_identifier'], $second['group_identifier'] );
                return $byGroup !== 0 ? $byGroup : $first['priority'] - $second['priority'];
            } );

            return $rows;
        }

        $rows = $db->arrayQuery( 'SELECT g.identifier AS group_identifier, s.identifier AS state_identifier,'
            . ' s.id, s.priority FROM ezcobj_state s'
            . ' JOIN ezcobj_state_group g ON s.group_id = g.id'
            . ' ORDER BY g.identifier, s.priority' );

        return is_array( $rows ) ? $rows : array();
    }

    /**
     * Map object state keys (e.g. 'ez_lock|not_locked') to their numeric IDs.
     */
    static function resolveObjectStateIds( $stateKeys )
    {
        static $map = null;
        // An empty map is deliberately not cached. Caching one turned a single
        // unreadable state list into a filter that matched no node for the
        // rest of the request.
        if ( !$map )
        {
            $map = array();
            foreach ( self::fetchObjectStateRows() as $state )
            {
                $key = (string)$state['group_identifier'] . '|' . (string)$state['state_identifier'];
                $map[$key] = (int)$state['id'];
            }
        }

        $ids = array();
        foreach ( array_values( (array)$stateKeys ) as $key )
        {
            if ( isset( $map[(string)$key] ) )
                $ids[] = $map[(string)$key];
        }
        return array_unique( $ids );
    }

    static function currentNode()
    {
        if ( isset( $_GET['node_id'] ) && is_numeric( $_GET['node_id'] )
             && isset( $_SERVER['REQUEST_URI'] )
             && strpos( $_SERVER['REQUEST_URI'], '/ezjscore/call' ) !== false )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$_GET['node_id'] );
            if ( $node instanceof eZContentObjectTreeNode )
                return $node;
        }
        $uri = eZSys::requestURI();
        if ( class_exists( 'expLayoutsResolver' ) )
            return expLayoutsResolver::nodeFromPath( $uri );
        return false;
    }

    /**
     * content_by_topic: content sharing the topic tag(s) of the current page,
     * resolved through the imported eztags link table.
     */
    static function contentByTopic( array $params, $offset = 0, $limit = 0 )
    {
        $db = eZDB::instance();

        $tagIds = array();
        if ( !empty( $params['topic_content_id'] ) )
        {
            $topicObjectId = (int)$params['topic_content_id'];
            $rows = $db->arrayQuery( 'SELECT keyword_id FROM eztags_attribute_link WHERE object_id=' . $topicObjectId );
            foreach ( $rows as $row )
                $tagIds[] = (int)$row['keyword_id'];
        }
        if ( empty( $tagIds ) )
        {
            $current = self::currentNode();
            if ( !$current )
                return false;
            $rows = $db->arrayQuery( 'SELECT keyword_id FROM eztags_attribute_link WHERE object_id=' . (int)$current->attribute( 'contentobject_id' ) );
            foreach ( $rows as $row )
                $tagIds[] = (int)$row['keyword_id'];
        }
        if ( empty( $tagIds ) )
            return array( 'total' => 0, 'items' => array() );

        $parentNodeId = self::remapNodeId( isset( $params['parent_location_id'] ) ? $params['parent_location_id'] : 0 );
        $parentPath = '/1/2/';
        if ( $parentNodeId > 0 )
        {
            $parentNode = eZContentObjectTreeNode::fetch( $parentNodeId, false, false );
            if ( is_array( $parentNode ) && isset( $parentNode['path_string'] ) )
                $parentPath = $parentNode['path_string'];
        }

        $typeFilter = '';
        if ( !empty( $params['filter_by_content_type'] ) && !empty( $params['content_types'] ) )
        {
            $names = array();
            foreach ( array_values( (array)$params['content_types'] ) as $ident )
                $names[] = "'" . $db->escapeString( $ident ) . "'";
            if ( $names )
                $typeFilter = ' AND co.contentclass_id IN (SELECT id FROM ezcontentclass WHERE identifier IN (' . implode( ',', $names ) . '))';
        }

        $currentObjectId = 0;
        $current = self::currentNode();
        if ( $current )
            $currentObjectId = (int)$current->attribute( 'contentobject_id' );

        $where = 't.node_id = t.main_node_id'
               . ' AND tal.keyword_id IN (' . implode( ',', array_map( 'intval', $tagIds ) ) . ')'
               . " AND t.path_string LIKE '" . $db->escapeString( $parentPath ) . "%'"
               . ' AND t.node_id != ' . (int)$parentNodeId
               . ( $currentObjectId ? ' AND co.id != ' . $currentObjectId : '' )
               . ' AND co.contentclass_id != (SELECT id FROM ezcontentclass WHERE identifier=\'ng_topic\')'
               . $typeFilter;

        // MongoDB has no three-table JOIN, and the driver refuses SQL it cannot
        // translate rather than returning unfiltered rows. Left to fail, every
        // tag-driven page - the topic landing pages and the related lists -
        // came back empty. The same result is assembled here from a lookup
        // between the two collections that actually matter, with the tag
        // membership resolved first.
        if ( $db->databaseName() === 'mongo' )
        {
            $taggedObjectIds = array();
            foreach ( $db->arrayQuery( 'SELECT object_id FROM eztags_attribute_link WHERE keyword_id IN ('
                . implode( ',', array_map( 'intval', $tagIds ) ) . ')' ) as $row )
            {
                $taggedObjectIds[] = (int)$row['object_id'];
            }
            $taggedObjectIds = array_values( array_unique( $taggedObjectIds ) );
            if ( !$taggedObjectIds )
                return array( 'total' => 0, 'items' => array() );

            // A topic never lists itself among its own tagged content.
            $topicRows = $db->arrayQuery( "SELECT id FROM ezcontentclass WHERE identifier='ng_topic'" );
            $topicClassId = isset( $topicRows[0]['id'] ) ? (int)$topicRows[0]['id'] : 0;

            $wantedClassIds = array();
            if ( !empty( $params['filter_by_content_type'] ) && !empty( $params['content_types'] ) )
            {
                $names = array();
                foreach ( array_values( (array)$params['content_types'] ) as $ident )
                    $names[] = "'" . $db->escapeString( $ident ) . "'";
                if ( $names )
                {
                    foreach ( $db->arrayQuery( 'SELECT id FROM ezcontentclass WHERE identifier IN ('
                        . implode( ',', $names ) . ')' ) as $row )
                    {
                        $wantedClassIds[] = (int)$row['id'];
                    }
                    // The filter named types that do not exist, so nothing matches.
                    if ( !$wantedClassIds )
                        return array( 'total' => 0, 'items' => array() );
                }
            }

            $objectConditions = array();
            if ( $topicClassId )
                $objectConditions[] = array( '_obj.contentclass_id' => array( '$ne' => $topicClassId ) );
            if ( $currentObjectId )
                $objectConditions[] = array( '_obj.id' => array( '$ne' => $currentObjectId ) );
            if ( $wantedClassIds )
                $objectConditions[] = array( '_obj.contentclass_id' => array( '$in' => $wantedClassIds ) );

            $pipeline = array(
                array( '$match' => array(
                    'contentobject_id' => array( '$in' => $taggedObjectIds ),
                    'path_string' => new MongoDB\BSON\Regex( '^' . preg_quote( $parentPath ), '' ),
                    'node_id' => array( '$ne' => (int)$parentNodeId ),
                    // t.node_id = t.main_node_id: main locations only.
                    '$expr' => array( '$eq' => array( '$node_id', '$main_node_id' ) ),
                ) ),
                array( '$lookup' => array(
                    'from' => 'ezcontentobject',
                    'localField' => 'contentobject_id',
                    'foreignField' => 'id',
                    'as' => '_obj',
                ) ),
                array( '$unwind' => '$_obj' ),
            );
            if ( $objectConditions )
                $pipeline[] = array( '$match' => array( '$and' => $objectConditions ) );

            // SELECT DISTINCT t.node_id ... ORDER BY co.published DESC
            $pipeline[] = array( '$group' => array(
                '_id' => '$node_id', 'published' => array( '$max' => '$_obj.published' ) ) );
            $pipeline[] = array( '$sort' => array( 'published' => -1, '_id' => 1 ) );

            $rows = $db->aggregate( 'ezcontentobject_tree', $pipeline );
            if ( !is_array( $rows ) )
                $rows = array();

            $total = count( $rows );
            if ( $limit > 0 )
                $rows = array_slice( $rows, (int)$offset, (int)$limit );

            $orderedIds = array();
            foreach ( $rows as $row )
                $orderedIds[] = (int) $row['_id'];

            return array( 'total' => $total, 'items' => self::fetchNodesInOrder( $orderedIds ) );
        }

        $countSql = 'SELECT COUNT(DISTINCT t.node_id) AS count FROM ezcontentobject_tree t'
                  . ' JOIN ezcontentobject co ON co.id = t.contentobject_id'
                  . ' JOIN eztags_attribute_link tal ON tal.object_id = co.id'
                  . ' WHERE ' . $where;
        $countRows = $db->arrayQuery( $countSql );
        $total = isset( $countRows[0]['count'] ) ? (int)$countRows[0]['count'] : 0;

        $sql = 'SELECT DISTINCT t.node_id, co.published FROM ezcontentobject_tree t'
             . ' JOIN ezcontentobject co ON co.id = t.contentobject_id'
             . ' JOIN eztags_attribute_link tal ON tal.object_id = co.id'
             . ' WHERE ' . $where
             . ' ORDER BY co.published DESC';

        $queryParams = array();
        if ( $limit > 0 )
        {
            $queryParams['limit'] = $limit;
            $queryParams['offset'] = $offset;
        }
        $rows = $db->arrayQuery( $sql, $queryParams );
        $orderedIds = array();
        foreach ( $rows as $row )
            $orderedIds[] = (int) $row['node_id'];

        return array( 'total' => $total, 'items' => self::fetchNodesInOrder( $orderedIds ) );
    }
}
