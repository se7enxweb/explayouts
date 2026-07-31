<?php

abstract class expLayoutsAbstractContentBlockHandler implements expLayoutsBlockHandlerInterface
{
    protected function fetchItems( $parameters, $block = false )
    {
        // Imported nglayouts dynamic collections carry their own query row;
        // execute it ahead of the block-level query_type parameter.
        if ( is_array( $block ) && isset( $block['id'] ) )
        {
            $dynCollection = expLayoutsCollection::fetchByBlock( (int)$block['id'] );
            if ( $dynCollection && $dynCollection->attribute( 'collection_type' ) === 'dynamic' )
            {
                $dynResult = expLayoutsDynamicCollection::fetch( $dynCollection );
                if ( $dynResult !== false )
                    return $dynResult;
            }
        }

        $queryType = isset( $parameters['query_type'] ) ? trim( $parameters['query_type'] ) : 'children';
        $handler = expLayoutsQueryHandlerFactory::get( $queryType );

        if ( $handler )
        {
            $params = $parameters;

            if ( $queryType === 'manual' && is_array( $block ) && isset( $block['id'] ) )
            {
                $collection = expLayoutsCollection::fetchByBlock( (int)$block['id'] );
                if ( $collection )
                    $params['collection_id'] = (int)$collection->attribute( 'id' );
            }

            if ( !isset( $params['node_id'] ) || (int)$params['node_id'] === 0 )
                $params['node_id'] = isset( $params['parent_node_id'] ) ? (int)$params['parent_node_id'] : 0;

            return $handler->fetch( $params );
        }

        $parentNodeId = isset( $parameters['parent_node_id'] ) ? (int)$parameters['parent_node_id'] : 0;
        if ( $parentNodeId <= 0 )
            return array( 'total' => 0, 'items' => array() );

        $options = array(
            'limit' => isset( $parameters['limit'] ) ? (int)$parameters['limit'] : 10,
            'offset' => isset( $parameters['offset'] ) ? (int)$parameters['offset'] : 0,
            'class_filter' => isset( $parameters['class_filter'] ) ? trim( $parameters['class_filter'] ) : '',
            'sort' => isset( $parameters['sort'] ) ? $parameters['sort'] : 'published',
        );

        return expLayoutsSiteAPI::filterChildren( $parentNodeId, $options );
    }

    protected function getCommonParameters()
    {
        return array(
            'query_type' => array(
                'name' => 'Query type',
                'type' => 'string',
                'default' => 'children',
            ),
            'parent_node_id' => array(
                'name' => 'Parent node ID',
                'type' => 'integer',
                'default' => 0,
            ),
            'node_id' => array(
                'name' => 'Node ID',
                'type' => 'integer',
                'default' => 0,
            ),
            'limit' => array(
                'name' => 'Limit',
                'type' => 'integer',
                'default' => 10,
            ),
            'offset' => array(
                'name' => 'Offset',
                'type' => 'integer',
                'default' => 0,
            ),
            'class_filter' => array(
                'name' => 'Class filter',
                'type' => 'string',
                'default' => '',
            ),
            'sort' => array(
                'name' => 'Sort',
                'type' => 'string',
                'default' => 'published',
            ),
        );
    }
}
