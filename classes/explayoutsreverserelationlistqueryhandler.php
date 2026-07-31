<?php

class expLayoutsReverseRelationListQueryHandler
{
    public static function getParameters()
    {
        return array(
            'use_current_location' => array( 'type' => 'checkbox', 'default' => '1' ),
            'location_id' => array( 'type' => 'text', 'default' => '' ),
            'sort_type' => array( 'type' => 'select', 'options' => array( 'date_published', 'date_modified', 'content_name' ), 'default' => 'date_published' ),
            'sort_direction' => array( 'type' => 'select', 'options' => array( 'desc', 'asc' ), 'default' => 'desc' ),
            'only_main_locations' => array( 'type' => 'checkbox', 'default' => '1' ),
            'filter_by_content_type' => array( 'type' => 'checkbox', 'default' => '0' ),
            'content_types' => array( 'type' => 'text', 'default' => '' ),
            'content_types_filter' => array( 'type' => 'select', 'options' => array( 'include', 'exclude' ), 'default' => 'include' ),
            'field_definition_identifier' => array( 'type' => 'text', 'default' => '' ),
        );
    }

    public static function getValues( $params, $offset = 0, $limit = null )
    {
        $object = self::selectedObject( $params );
        if ( !$object instanceof eZContentObject )
            return array();

        $fieldIdentifier = isset( $params['field_definition_identifier'] ) ? trim( $params['field_definition_identifier'] ) : '';
        $reverse = eZContentObject::reverseRelatedObjects( (int)$object->attribute( 'id' ), false, 0, $fieldIdentifier );
        if ( !is_array( $reverse ) )
            return array();

        $nodes = array();
        foreach ( $reverse as $relatedObject )
        {
            if ( !$relatedObject instanceof eZContentObject )
                continue;

            $node = !empty( $params['only_main_locations'] ) ? $relatedObject->mainNode() : eZContentObjectTreeNode::fetchByContentObjectID( (int)$relatedObject->attribute( 'id' ) );
            if ( is_array( $node ) )
                $node = count( $node ) > 0 ? $node[0] : false;

            if ( !$node instanceof eZContentObjectTreeNode )
                continue;

            if ( !self::filterContentType( $relatedObject, $params ) )
                continue;

            $nodes[] = $node;
        }

        return self::sortNodes( $nodes, $params );
    }

    public static function getCount( $params )
    {
        return count( self::getValues( $params ) );
    }

    public static function isContextual( $params )
    {
        return !empty( $params['use_current_location'] );
    }

    protected static function selectedObject( $params )
    {
        if ( !empty( $params['use_current_location'] ) )
            return expLayoutsExpSiteApi::currentObject();

        $locationId = isset( $params['location_id'] ) ? (int)$params['location_id'] : 0;
        $node = eZContentObjectTreeNode::fetch( $locationId );
        if ( $node instanceof eZContentObjectTreeNode )
            return $node->attribute( 'object' );
        return false;
    }

    protected static function filterContentType( eZContentObject $object, $params )
    {
        if ( empty( $params['filter_by_content_type'] ) )
            return true;

        $contentTypes = isset( $params['content_types'] ) ? explode( ',', $params['content_types'] ) : array();
        $contentTypes = array_map( 'trim', $contentTypes );
        if ( count( $contentTypes ) === 0 )
            return true;

        $matches = in_array( $object->attribute( 'class_identifier' ), $contentTypes );
        $filter = isset( $params['content_types_filter'] ) ? $params['content_types_filter'] : 'include';
        return $filter === 'exclude' ? !$matches : $matches;
    }

    protected static function sortNodes( $nodes, $params )
    {
        $sortType = isset( $params['sort_type'] ) ? $params['sort_type'] : 'date_published';
        $direction = isset( $params['sort_direction'] ) && $params['sort_direction'] === 'asc' ? 'asc' : 'desc';

        $map = array(
            'date_published' => 'attribute_published',
            'date_modified' => 'attribute_modified',
            'content_name' => 'attribute_name',
        );

        if ( !isset( $map[$sortType] ) )
            return $nodes;

        usort( $nodes, function ( $a, $b ) use ( $map, $sortType, $direction ) {
            $attr = $map[$sortType];
            $va = (string)$a->attribute( $attr );
            $vb = (string)$b->attribute( $attr );
            $cmp = strnatcasecmp( $va, $vb );
            return $direction === 'asc' ? $cmp : -$cmp;
        } );

        return $nodes;
    }
}