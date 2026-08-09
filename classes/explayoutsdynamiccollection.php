<?php
/**
 * Executes 'dynamic' collections (imported nglayouts collection queries).
 *
 * Supported query types:
 *  - ibexa_content_search: subtree fetch with content-type filter, publish-date
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
            case 'ibexa_content_search':
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

        if ( $result !== false )
            $result = self::applyPinnedItems( $collectionId, $result, $limit );
        return $result;
    }

    /**
     * Manual items stored on a dynamic collection are pinned overrides: they
     * occupy their stored position, query results fill the slots around them.
     */
    static function applyPinnedItems( $collectionId, $result, $limit = 0 )
    {
        $pinned = array();
        foreach ( expLayoutsCollectionItem::fetchByCollection( $collectionId, true ) as $item )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$item->attribute( 'value_id' ) );
            if ( $node )
                $pinned[(int)$item->attribute( 'position' )] = $node;
        }
        if ( empty( $pinned ) )
            return $result;

        $queryItems = $result['items'];
        $pinnedIds = array();
        foreach ( $pinned as $node )
            $pinnedIds[] = (int)$node->attribute( 'node_id' );

        $merged = array();
        $qi = 0;
        $total = count( $queryItems ) + count( $pinned );
        for ( $pos = 0; $pos < $total; $pos++ )
        {
            if ( isset( $pinned[$pos] ) )
            {
                $merged[] = $pinned[$pos];
                continue;
            }
            while ( $qi < count( $queryItems ) && in_array( (int)$queryItems[$qi]->attribute( 'node_id' ), $pinnedIds ) )
                $qi++;
            if ( $qi < count( $queryItems ) )
            {
                $merged[] = $queryItems[$qi];
                $qi++;
            }
        }
        if ( $limit > 0 )
            $merged = array_slice( $merged, 0, $limit );
        return array( 'total' => $result['total'], 'items' => $merged );
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
        $nodes = array();
        foreach ( $items as $item )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$item->attribute( 'value_id' ) );
            if ( $node )
                $nodes[] = $node;
        }
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
        $ini = eZINI::instance( 'explayouts' );
        $key = (string)$nexusId;
        if ( $ini->hasVariable( 'NexusNodeMap', $key ) )
        {
            $mapped = (int)$ini->variable( 'NexusNodeMap', $key );
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
        $sortAsc = ( isset( $params['sort_direction'] ) && strtolower( (string)$params['sort_direction'] ) === 'ascending' );

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

        // over-fetch a little so exclude+offset+limit still fill the page
        $fetchParams['Limit'] = ( $limit > 0 ? $limit : 50 ) + $offset + ( $excludeNodeId ? 1 : 0 );
        $fetchParams['Offset'] = 0;

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

        $total = eZContentObjectTreeNode::subTreeCountByNodeID( $fetchParams, $parentNodeId );
        if ( $total === null )
            $total = 0;
        $nodes = array_slice( $nodes, $offset, $limit > 0 ? $limit : null );
        return array( 'total' => $total, 'items' => $nodes );
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
               . ( $currentObjectId ? ' AND co.id != ' . $currentObjectId : '' )
               . ' AND co.contentclass_id != (SELECT id FROM ezcontentclass WHERE identifier=\'ng_topic\')'
               . $typeFilter;

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
        $nodes = array();
        foreach ( $rows as $row )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$row['node_id'] );
            if ( $node )
                $nodes[] = $node;
        }
        return array( 'total' => $total, 'items' => $nodes );
    }
}
