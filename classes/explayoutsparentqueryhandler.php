<?php
class expLayoutsParentQueryHandler implements expLayoutsQueryHandlerInterface
{
    public function getName()
    {
        return 'Parent of a node';
    }

    public function fetch( $parameters )
    {
        $nodeId = isset( $parameters['node_id'] ) ? (int)$parameters['node_id'] : 0;
        if ( $nodeId <= 0 )
            return array( 'total' => 0, 'items' => array() );

        $node = eZContentObjectTreeNode::fetch( $nodeId, false );
        if ( !$node || !isset( $node['parent_node_id'] ) )
            return array( 'total' => 0, 'items' => array() );

        $parent = eZContentObjectTreeNode::fetch( (int)$node['parent_node_id'] );
        if ( !$parent )
            return array( 'total' => 0, 'items' => array() );

        return array( 'total' => 1, 'items' => array( $parent ) );
    }
}
