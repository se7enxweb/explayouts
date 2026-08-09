<?php
class expLayoutsManualQueryHandler implements expLayoutsQueryHandlerInterface
{
    public function getName()
    {
        return 'Manual collection';
    }

    public function fetch( $parameters )
    {
        $collectionId = isset( $parameters['collection_id'] ) ? (int)$parameters['collection_id'] : 0;
        if ( $collectionId <= 0 )
            return array( 'total' => 0, 'items' => array() );

        $items = expLayoutsCollectionItem::fetchByCollection( $collectionId, true );
        $nodes = array();
        foreach ( $items as $item )
        {
            $valueId = (int)$item->attribute( 'value_id' );
            if ( $valueId <= 0 )
                continue;

            $node = eZContentObjectTreeNode::fetch( $valueId );
            if ( $node )
                $nodes[] = $node;
        }

        // honour the collection's stored offset/limit like the reference does
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT offset_value, limit_value FROM explayouts_collection WHERE id=' . (int)$collectionId );
        if ( isset( $rows[0] ) )
        {
            $sliceOffset = (int)$rows[0]['offset_value'];
            $sliceLimit = (int)$rows[0]['limit_value'];
            if ( $sliceOffset > 0 || $sliceLimit > 0 )
            {
                $total = count( $nodes );
                $nodes = array_slice( $nodes, $sliceOffset, $sliceLimit > 0 ? $sliceLimit : null );
                return array( 'total' => $total, 'items' => $nodes );
            }
        }

        return array( 'total' => count( $nodes ), 'items' => $nodes );
    }
}
