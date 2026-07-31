<?php
class expLayoutsRandomQueryHandler implements expLayoutsQueryHandlerInterface
{
    public function getName()
    {
        return 'Random content';
    }

    public function fetch( $parameters )
    {
        $parentNodeId = isset( $parameters['parent_node_id'] ) ? (int)$parameters['parent_node_id'] : 0;
        $limit = isset( $parameters['limit'] ) ? (int)$parameters['limit'] : 5;

        if ( $parentNodeId <= 0 )
            return array( 'total' => 0, 'items' => array() );

        $params = array(
            'Depth' => 1,
            'Limit' => 100,
            'SortBy' => array( 'published', false ),
        );

        $items = eZContentObjectTreeNode::subTreeByNodeID( $params, $parentNodeId );
        if ( !is_array( $items ) )
            $items = array();

        shuffle( $items );
        $items = array_slice( $items, 0, $limit );

        return array( 'total' => count( $items ), 'items' => $items );
    }
}
