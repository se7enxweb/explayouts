<?php

/**
 * Exponential 4 query builders compatible with the Exp Site API QueryType pattern.
 *
 * Each method returns an array that can be passed to expLayoutsSiteAPI::filterContent()
 * or expLayoutsSiteAPI::filterLocations().
 */
class expLayoutsQueryType
{
    /**
     * Children of a parent location.
     */
    public static function children( $parentNodeId, array $options = array() )
    {
        $query = array(
            'parent_node_id' => (int)$parentNodeId,
            'depth' => 1,
            'limit' => isset( $options['limit'] ) ? (int)$options['limit'] : 25,
            'offset' => isset( $options['offset'] ) ? (int)$options['offset'] : 0,
            'sort' => isset( $options['sort'] ) ? $options['sort'] : 'published',
        );

        if ( isset( $options['class_filter'] ) )
            $query['class_filter'] = $options['class_filter'];

        if ( isset( $options['attribute_filter'] ) )
            $query['attribute_filter'] = $options['attribute_filter'];

        return $query;
    }

    /**
     * Siblings of a location (same parent, excluding the location itself).
     */
    public static function siblings( $locationId, array $options = array() )
    {
        $locationId = (int)$locationId;
        $node = eZContentObjectTreeNode::fetch( $locationId );
        $parentNodeId = $node ? (int)$node->attribute( 'parent_node_id' ) : 0;

        $query = array(
            'parent_node_id' => $parentNodeId,
            'depth' => 1,
            'limit' => isset( $options['limit'] ) ? (int)$options['limit'] : 25,
            'offset' => isset( $options['offset'] ) ? (int)$options['offset'] : 0,
            'sort' => isset( $options['sort'] ) ? $options['sort'] : 'published',
            'exclude_node_id' => $locationId,
        );

        if ( isset( $options['class_filter'] ) )
            $query['class_filter'] = $options['class_filter'];

        if ( isset( $options['attribute_filter'] ) )
            $query['attribute_filter'] = $options['attribute_filter'];

        return $query;
    }

    /**
     * Full subtree under a location.
     */
    public static function subtree( $locationId, array $options = array() )
    {
        $query = array(
            'parent_node_id' => (int)$locationId,
            'depth' => isset( $options['depth'] ) ? (int)$options['depth'] : 10,
            'limit' => isset( $options['limit'] ) ? (int)$options['limit'] : 25,
            'offset' => isset( $options['offset'] ) ? (int)$options['offset'] : 0,
            'sort' => isset( $options['sort'] ) ? $options['sort'] : 'published',
        );

        if ( isset( $options['class_filter'] ) )
            $query['class_filter'] = $options['class_filter'];

        if ( isset( $options['attribute_filter'] ) )
            $query['attribute_filter'] = $options['attribute_filter'];

        return $query;
    }

    /**
     * Full-text search query.
     */
    public static function fullText( $text, array $options = array() )
    {
        return array(
            'full_text' => (string)$text,
            'limit' => isset( $options['limit'] ) ? (int)$options['limit'] : 25,
            'offset' => isset( $options['offset'] ) ? (int)$options['offset'] : 0,
        );
    }

    /**
     * Fetch a single content by ID or remote ID. Use with filterContent() / findContent().
     */
    public static function contentById( $contentId )
    {
        return array( 'content_id' => (int)$contentId );
    }

    /**
     * Related Content from a field.
     */
    public static function fieldRelations( $contentId, $fieldDefinitionIdentifier, array $options = array() )
    {
        return array(
            'relation_content_id' => (int)$contentId,
            'relation_field' => (string)$fieldDefinitionIdentifier,
            'relation_type' => 'forward',
            'limit' => isset( $options['limit'] ) ? (int)$options['limit'] : 25,
            'offset' => isset( $options['offset'] ) ? (int)$options['offset'] : 0,
        );
    }

    /**
     * Reverse related Content from a field.
     */
    public static function reverseFieldRelations( $contentId, $fieldDefinitionIdentifier, array $options = array() )
    {
        return array(
            'relation_content_id' => (int)$contentId,
            'relation_field' => (string)$fieldDefinitionIdentifier,
            'relation_type' => 'reverse',
            'limit' => isset( $options['limit'] ) ? (int)$options['limit'] : 25,
            'offset' => isset( $options['offset'] ) ? (int)$options['offset'] : 0,
        );
    }
}
