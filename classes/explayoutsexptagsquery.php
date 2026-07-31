<?php

/**
 * eZ Publish 4 port of Netgen Layouts Exp TagsQuery handler.
 */
class expLayoutsExpTagsQuery
{
    public static function getValues( $tagOrKeyword, array $options = array() )
    {
        $parentNodeId = isset( $options['parent_node_id'] ) ? (int)$options['parent_node_id'] : (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' );
        $offset = isset( $options['offset'] ) ? (int)$options['offset'] : 0;
        $limit = isset( $options['limit'] ) ? (int)$options['limit'] : null;
        $logic = isset( $options['tags_filter_logic'] ) ? $options['tags_filter_logic'] : 'any';

        $parentNode = expLayoutsSiteAPI::loadLocation( $parentNodeId );
        if ( !$parentNode instanceof eZContentObjectTreeNode )
            return array();

        $keywords = self::prepareKeywords( $tagOrKeyword );
        if ( count( $keywords ) === 0 )
            return array();

        $results = self::findByKeywords( $parentNode, $keywords, $logic );
        $results = array_slice( $results, $offset, $limit );

        return $results;
    }

    public static function getCount( $tagOrKeyword, array $options = array() )
    {
        return count( self::getValues( $tagOrKeyword, $options ) );
    }

    private static function prepareKeywords( $input )
    {
        $keywords = array();
        foreach ( (array)$input as $item )
        {
            $item = trim( $item );
            if ( $item !== '' )
                $keywords[] = $item;
        }
        return array_unique( array_values( $keywords ) );
    }

    private static function findByKeywords( eZContentObjectTreeNode $parentNode, array $keywords, $logic )
    {
        $results = array();
        $params = array(
            'Limit' => 1000,
            'Offset' => 0,
            'Depth' => 10,
        );

        $nodes = $parentNode->subTree( $params );

        foreach ( $nodes as $node )
        {
            if ( !$node instanceof eZContentObjectTreeNode )
                continue;

            $content = $node->object();
            if ( !$content instanceof eZContentObject )
                continue;

            $matched = self::matchesKeywords( $content, $keywords, $logic );
            if ( $matched )
                $results[] = $node;
        }

        return $results;
    }

    private static function matchesKeywords( eZContentObject $content, array $keywords, $logic )
    {
        $dataMap = $content->attribute( 'data_map' );
        $found = array();
        foreach ( $dataMap as $identifier => $attribute )
        {
            if ( !is_object( $attribute ) )
                continue;

            $text = '';
            $attrContent = $attribute->content();
            if ( is_string( $attrContent ) )
                $text = $attrContent;
            elseif ( is_array( $attrContent ) )
                $text = implode( ' ', $attrContent );

            foreach ( $keywords as $keyword )
            {
                if ( stripos( $text, $keyword ) !== false )
                    $found[$keyword] = true;
            }
        }

        if ( $logic === 'all' )
            return count( $found ) === count( $keywords );

        return count( $found ) > 0;
    }
}
