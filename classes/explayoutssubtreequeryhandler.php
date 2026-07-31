<?php
class expLayoutsSubtreeQueryHandler implements expLayoutsQueryHandlerInterface
{
    public function getName()
    {
        return 'Subtree of a node';
    }

    public function fetch( $parameters )
    {
        $parentNodeId = isset( $parameters['parent_node_id'] ) ? (int)$parameters['parent_node_id'] : 0;
        $limit = isset( $parameters['limit'] ) ? (int)$parameters['limit'] : 10;
        $offset = isset( $parameters['offset'] ) ? (int)$parameters['offset'] : 0;

        if ( $parentNodeId <= 0 )
            return array( 'total' => 0, 'items' => array() );

        $sort = self::parseSort( isset( $parameters['sort'] ) ? $parameters['sort'] : 'published' );
        $params = array(
            'Depth' => 10,
            'Limit' => $limit,
            'Offset' => $offset,
            'SortBy' => $sort,
        );

        $classFilter = isset( $parameters['class_filter'] ) ? trim( $parameters['class_filter'] ) : '';
        if ( $classFilter !== '' )
        {
            $classFilterArray = is_array( $classFilter ) ? $classFilter : array_filter( array_map( 'trim', explode( ',', $classFilter ) ) );
            if ( !empty( $classFilterArray ) )
            {
                $filterType = isset( $parameters['class_filter_type'] ) ? $parameters['class_filter_type'] : 'include';
                $params['ClassFilterType'] = in_array( $filterType, array( 'include', 'exclude' ) ) ? $filterType : 'include';
                $params['ClassFilterArray'] = $classFilterArray;
            }
        }

        $items = eZContentObjectTreeNode::subTreeByNodeID( $params, $parentNodeId );
        if ( !is_array( $items ) )
            $items = array();

        return array( 'total' => count( $items ), 'items' => $items );
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
