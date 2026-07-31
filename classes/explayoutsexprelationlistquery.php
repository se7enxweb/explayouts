<?php

/**
 * eZ Publish 4 port of Netgen Layouts Exp RelationListQuery handler.
 */
class expLayoutsExpRelationListQuery
{
    public static function getValues( $content, $fieldIdentifier, array $options = array() )
    {
        $contentObject = expLayoutsSiteAPI::getContentObject( $content );
        if ( !$contentObject instanceof eZContentObject )
            return array();

        $contentTypeFilter = isset( $options['content_types'] ) ? (array)$options['content_types'] : array();
        $filterType = isset( $options['content_types_filter'] ) ? $options['content_types_filter'] : 'include';
        $onlyMainLocations = isset( $options['only_main_locations'] ) ? (bool)$options['only_main_locations'] : true;
        $offset = isset( $options['offset'] ) ? (int)$options['offset'] : 0;
        $limit = isset( $options['limit'] ) ? (int)$options['limit'] : null;

        $relatedObjects = expLayoutsSiteAPI::loadFieldRelations( $contentObject, $fieldIdentifier, array(), null );

        $results = array();
        foreach ( $relatedObjects as $object )
        {
            if ( !$object instanceof eZContentObject )
                continue;

            $classIdentifier = $object->attribute( 'class_identifier' );
            if ( count( $contentTypeFilter ) > 0 )
            {
                $inFilter = in_array( $classIdentifier, $contentTypeFilter, true );
                if ( $filterType === 'include' && !$inFilter )
                    continue;
                if ( $filterType === 'exclude' && $inFilter )
                    continue;
            }

            if ( $onlyMainLocations )
            {
                $node = $object->mainNode();
                if ( !$node instanceof eZContentObjectTreeNode )
                    continue;
                $results[] = $node;
            }
            else
            {
                $nodes = $object->assignedNodes();
                foreach ( $nodes as $node )
                {
                    $results[] = $node;
                }
            }
        }

        self::sortResults( $results, isset( $options['sort_type'] ) ? $options['sort_type'] : 'date_published', isset( $options['sort_direction'] ) ? $options['sort_direction'] : 'desc' );

        $results = array_slice( $results, $offset, $limit );

        return $results;
    }

    public static function getCount( $content, $fieldIdentifier, array $options = array() )
    {
        return count( self::getValues( $content, $fieldIdentifier, $options ) );
    }

    private static function sortResults( array &$results, $sortType, $sortDirection )
    {
        $direction = strtolower( $sortDirection ) === 'asc' ? 1 : -1;

        usort( $results, function( $a, $b ) use ( $sortType, $direction ) {
            $getA = self::getSortValue( $a, $sortType );
            $getB = self::getSortValue( $b, $sortType );
            if ( $getA === $getB )
                return 0;
            return $direction * ( $getA < $getB ? -1 : 1 );
        } );
    }

    private static function getSortValue( $object, $sortType )
    {
        if ( !$object instanceof eZContentObjectTreeNode )
            return '';

        $content = $object->object();
        if ( !$content instanceof eZContentObject )
            return '';

        switch ( $sortType )
        {
            case 'date_modified':
                return (int)$content->attribute( 'modified' );
            case 'content_name':
                return strtolower( $content->attribute( 'name' ) );
            case 'date_published':
            default:
                return (int)$content->attribute( 'published' );
        }
    }
}
