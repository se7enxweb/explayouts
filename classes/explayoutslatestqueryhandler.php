<?php
class expLayoutsLatestQueryHandler implements expLayoutsQueryHandlerInterface
{
    public function getName()
    {
        return 'Latest content';
    }

    public function fetch( $parameters )
    {
        $parentNodeId = isset( $parameters['parent_node_id'] ) ? (int)$parameters['parent_node_id'] : 0;
        $limit = isset( $parameters['limit'] ) ? (int)$parameters['limit'] : 10;
        $offset = isset( $parameters['offset'] ) ? (int)$parameters['offset'] : 0;
        $classFilter = isset( $parameters['class_filter'] ) ? trim( $parameters['class_filter'] ) : '';

        if ( $parentNodeId <= 0 )
            return array( 'total' => 0, 'items' => array() );

        $params = array(
            'Depth' => 1,
            'Limit' => $limit,
            'Offset' => $offset,
            'SortBy' => array( 'published', false ),
        );

        if ( $classFilter !== '' )
        {
            $params['ClassFilterType'] = 'include';
            $params['ClassFilterArray'] = array( $classFilter );
        }

        $items = eZContentObjectTreeNode::subTreeByNodeID( $params, $parentNodeId );
        if ( !is_array( $items ) )
            $items = array();

        return array( 'total' => count( $items ), 'items' => $items );
    }
}
