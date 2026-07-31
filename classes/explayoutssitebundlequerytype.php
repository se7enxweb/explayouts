<?php


class expLayoutsSiteBundleQueryType
{
    public static function getQuery( $name, $params = array() )
    {
        switch ( $name )
        {
            case 'children':
                return isset( $params['parent_node_id'] )
                    ? expLayoutsQueryType::children( (int)$params['parent_node_id'], $params )
                    : array();

            case 'siblings':
                return isset( $params['location_id'] )
                    ? expLayoutsQueryType::siblings( (int)$params['location_id'], $params )
                    : array();

            case 'subtree':
                return isset( $params['location_id'] )
                    ? expLayoutsQueryType::subtree( (int)$params['location_id'], $params )
                    : array();

            case 'full_text':
                return isset( $params['text'] )
                    ? expLayoutsQueryType::fullText( $params['text'], $params )
                    : array();

            case 'content_by_id':
                return isset( $params['content_id'] )
                    ? expLayoutsQueryType::contentById( (int)$params['content_id'] )
                    : array();

            case 'field_relations':
                return isset( $params['content_id'], $params['field'] )
                    ? expLayoutsQueryType::fieldRelations( (int)$params['content_id'], $params['field'], $params )
                    : array();

            case 'reverse_field_relations':
                return isset( $params['content_id'], $params['field'] )
                    ? expLayoutsQueryType::reverseFieldRelations( (int)$params['content_id'], $params['field'], $params )
                    : array();
        }

        return array();
    }


    public static function build( $type, array $params = array() )
    {
        if ( method_exists( 'expLayoutsQueryType', $type ) )
            return call_user_func_array( array( 'expLayoutsQueryType', $type ), $params );
        return array();
    }
}
