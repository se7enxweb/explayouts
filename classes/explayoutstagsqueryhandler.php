<?php

class expLayoutsTagsQueryHandler
{
    public static function getParameters()
    {
        return array(
            'parent_node_id' => array( 'type' => 'text', 'default' => '2' ),
            'filter_by_tags' => array( 'type' => 'text', 'default' => '' ),
            'use_tags_from_current_content' => array( 'type' => 'checkbox', 'default' => '0' ),
            'field_definition_identifier' => array( 'type' => 'text', 'default' => '' ),
            'use_tags_from_query_string' => array( 'type' => 'checkbox', 'default' => '0' ),
            'query_string_param_name' => array( 'type' => 'text', 'default' => 'tag' ),
            'tags_filter_logic' => array( 'type' => 'select', 'options' => array( 'any', 'all' ), 'default' => 'any' ),
            'sort_type' => array( 'type' => 'select', 'options' => array( 'date_published', 'date_modified', 'content_name' ), 'default' => 'date_published' ),
            'sort_direction' => array( 'type' => 'select', 'options' => array( 'desc', 'asc' ), 'default' => 'desc' ),
            'only_main_locations' => array( 'type' => 'checkbox', 'default' => '1' ),
            'filter_by_content_type' => array( 'type' => 'checkbox', 'default' => '0' ),
            'content_types' => array( 'type' => 'text', 'default' => '' ),
            'content_types_filter' => array( 'type' => 'select', 'options' => array( 'include', 'exclude' ), 'default' => 'include' ),
        );
    }

    public static function getValues( $params, $offset = 0, $limit = null )
    {
        $tagIds = self::getTagIds( $params );
        if ( count( $tagIds ) === 0 )
            return array();

        $parentNodeId = isset( $params['parent_node_id'] ) ? (int)$params['parent_node_id'] : 2;
        return self::fetchByTags( $parentNodeId, $tagIds, $params, (int)$offset, $limit );
    }

    public static function getCount( $params )
    {
        return count( self::getValues( $params ) );
    }

    public static function isContextual( $params )
    {
        return !empty( $params['use_tags_from_current_content'] );
    }

    protected static function getTagIds( $params )
    {
        $tagIds = array();

        if ( !empty( $params['filter_by_tags'] ) )
        {
            $tagIds = array_merge( $tagIds, array_map( 'intval', explode( ',', $params['filter_by_tags'] ) ) );
        }

        if ( !empty( $params['use_tags_from_current_content'] ) )
        {
            $object = expLayoutsExpSiteApi::currentObject();
            if ( $object instanceof eZContentObject )
            {
                $fieldIdentifier = isset( $params['field_definition_identifier'] ) ? trim( $params['field_definition_identifier'] ) : '';
                $tagIds = array_merge( $tagIds, self::getTagsFromContent( $object, $fieldIdentifier ) );
            }
        }

        if ( !empty( $params['use_tags_from_query_string'] ) && isset( $_GET[$params['query_string_param_name']] ) )
        {
            $raw = $_GET[$params['query_string_param_name']];
            if ( is_array( $raw ) )
                $tagIds = array_merge( $tagIds, array_map( 'intval', $raw ) );
            else
                $tagIds = array_merge( $tagIds, array_map( 'intval', explode( ',', $raw ) ) );
        }

        return array_values( array_unique( array_filter( $tagIds ) ) );
    }

    protected static function getTagsFromContent( eZContentObject $object, $fieldIdentifier )
    {
        $ids = array();
        if ( $fieldIdentifier !== '' )
        {
            $attribute = $object->contentObjectAttributes( $fieldIdentifier );
            if ( is_array( $attribute ) && count( $attribute ) > 0 )
                $ids = self::extractTagIds( $attribute[0] );
        }
        else
        {
            foreach ( $object->contentObjectAttributes() as $attribute )
            {
                $ids = array_merge( $ids, self::extractTagIds( $attribute ) );
            }
        }
        return $ids;
    }

    protected static function extractTagIds( eZContentObjectAttribute $attribute )
    {
        $ids = array();
        $value = $attribute->attribute( 'content' );
        if ( is_array( $value ) )
        {
            foreach ( $value as $item )
            {
                if ( is_numeric( $item ) )
                    $ids[] = (int)$item;
            }
        }
        elseif ( method_exists( $value, 'attribute' ) && $value->hasAttribute( 'tags' ) )
        {
            $tags = $value->attribute( 'tags' );
            if ( is_array( $tags ) )
            {
                foreach ( $tags as $tag )
                {
                    if ( is_numeric( $tag ) )
                        $ids[] = (int)$tag;
                    elseif ( is_object( $tag ) && $tag->hasAttribute( 'id' ) )
                        $ids[] = (int)$tag->attribute( 'id' );
                }
            }
        }
        return $ids;
    }

    protected static function fetchByTags( $parentNodeId, $tagIds, $params, $offset, $limit )
    {
        if ( !class_exists( 'eZTagsObject' ) )
            return array();

        $contentIds = array();
        foreach ( $tagIds as $tagId )
        {
            $tag = eZTagsObject::fetch( $tagId );
            if ( !$tag instanceof eZTagsObject )
                continue;
            $contentIds = array_merge( $contentIds, $tag->getRelatedObjects() );
        }

        if ( count( $contentIds ) === 0 )
            return array();

        if ( !empty( $params['tags_filter_logic'] ) && $params['tags_filter_logic'] === 'all' )
        {
            $counts = array_count_values( $contentIds );
            $contentIds = array_keys( array_filter( $counts, function ( $count ) use ( $tagIds ) { return $count >= count( $tagIds ); } ) );
        }
        else
        {
            $contentIds = array_unique( $contentIds );
        }

        $nodes = array();
        foreach ( $contentIds as $contentId )
        {
            $object = eZContentObject::fetch( (int)$contentId );
            if ( !$object instanceof eZContentObject )
                continue;
            if ( !self::filterContentType( $object, $params ) )
                continue;

            $node = !empty( $params['only_main_locations'] ) ? $object->mainNode() : false;
            if ( !$node )
            {
                $list = eZContentObjectTreeNode::fetchByContentObjectID( (int)$contentId );
                $node = is_array( $list ) && count( $list ) > 0 ? $list[0] : false;
            }
            if ( $node instanceof eZContentObjectTreeNode )
                $nodes[] = $node;
        }

        return self::sortNodes( $nodes, $params );
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