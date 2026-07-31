<?php
class expLayoutsSiblingsQueryHandler implements expLayoutsQueryHandlerInterface
{
    public function getName()
    {
        return 'Siblings of a node';
    }

    public function fetch( $parameters )
    {
        $nodeId = isset( $parameters['node_id'] ) ? (int)$parameters['node_id'] : 0;
        $limit = isset( $parameters['limit'] ) ? (int)$parameters['limit'] : 10;
        $offset = isset( $parameters['offset'] ) ? (int)$parameters['offset'] : 0;

        if ( $nodeId <= 0 )
            return array( 'total' => 0, 'items' => array() );

        $node = eZContentObjectTreeNode::fetch( $nodeId, false );
        if ( !$node || !isset( $node['parent_node_id'] ) )
            return array( 'total' => 0, 'items' => array() );

        $params = array(
            'Depth' => 1,
            'Limit' => $limit,
            'Offset' => $offset,
            'SortBy' => array( 'published', false ),
        );

        $items = eZContentObjectTreeNode::subTreeByNodeID( $params, (int)$node['parent_node_id'] );
        if ( !is_array( $items ) )
            $items = array();

        $filtered = array();
        foreach ( $items as $item )
        {
            if ( (int)$item->attribute( 'node_id' ) !== $nodeId )
                $filtered[] = $item;
        }

        return array( 'total' => count( $filtered ), 'items' => $filtered );
    }
}
