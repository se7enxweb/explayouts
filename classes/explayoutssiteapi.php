<?php

/**
 * Exponential 4 / Exponential Site API compatibility layer.
 *
 * Mirrors the surface of netgen/expsite-api for use in legacy block handlers
 * and templates. Returns native eZ objects (eZContentObject, eZContentObjectTreeNode)
 * wrapped in the same method names as the Exp Site API.
 */
class expLayoutsSiteAPI
{
    const ABSOLUTE_URL = 0;
    const ABSOLUTE_PATH = 1;
    const RELATIVE_PATH = 2;
    const NETWORK_PATH = 3;

    /**
     * LoadService: load a Location by its node ID.
     */
    public static function loadLocation( $locationId )
    {
        return eZContentObjectTreeNode::fetch( (int)$locationId );
    }

    /**
     * LoadService: load a Content by its object ID.
     */
    public static function loadContent( $contentId )
    {
        return eZContentObject::fetch( (int)$contentId );
    }

    /**
     * LoadService: load a Content for preview by object ID, version and language.
     */
    public static function loadContentForPreview( $contentId, $versionNo, $languageCode )
    {
        $content = eZContentObject::fetch( (int)$contentId );
        if ( !$content )
            return false;

        $version = $content->version( (int)$versionNo );
        return $version ? $version : $content;
    }

    /**
     * LoadService: load a Content by its remote ID.
     */
    public static function loadContentByRemoteId( $remoteId )
    {
        return eZContentObject::fetchByRemoteID( (string)$remoteId );
    }

    /**
     * LoadService: load a Location by its remote ID.
     */
    public static function loadLocationByRemoteId( $remoteId )
    {
        $content = eZContentObject::fetchByRemoteID( (string)$remoteId );
        if ( !$content )
            return false;

        $node = $content->mainNode();
        return $node ? $node : false;
    }

    /**
     * FilterService: filter Content objects with a legacy query array.
     *
     * Supported query keys: parent_node_id, depth, class_filter, limit, offset,
     * sort, attribute_filter, full_text.
     */
    public static function filterContent( $query )
    {
        return self::runSearch( $query, 'content' );
    }

    /**
     * FilterService: filter Location objects with a legacy query array.
     */
    public static function filterLocations( $query )
    {
        return self::runSearch( $query, 'location' );
    }

    /**
     * FindService: find Content objects using the configured search engine.
     */
    public static function findContent( $query )
    {
        return self::runSearch( $query, 'content' );
    }

    /**
     * FindService: find Location objects using the configured search engine.
     */
    public static function findLocations( $query )
    {
        return self::runSearch( $query, 'location' );
    }

    /**
     * RelationService: load a single related Content from a relation field.
     */
    public static function loadFieldRelation( $content, $fieldDefinitionIdentifier, $contentTypeIdentifiers = array() )
    {
        $relations = self::loadFieldRelations( $content, $fieldDefinitionIdentifier, $contentTypeIdentifiers, 1 );
        return !empty( $relations ) ? $relations[0] : false;
    }

    /**
     * RelationService: load all related Content from a relation field.
     */
    public static function loadFieldRelations( $content, $fieldDefinitionIdentifier, $contentTypeIdentifiers = array(), $limit = null )
    {
        $contentObject = self::getContentObject( $content );
        if ( !$contentObject )
            return array();

        $dataMap = $contentObject->attribute( 'data_map' );
        if ( !isset( $dataMap[$fieldDefinitionIdentifier] ) || !is_object( $dataMap[$fieldDefinitionIdentifier] ) )
            return array();

        $field = $dataMap[$fieldDefinitionIdentifier];
        $relatedIDs = self::extractRelationIds( $field );
        if ( empty( $relatedIDs ) )
            return array();

        $items = array();
        foreach ( $relatedIDs as $id )
        {
            $object = eZContentObject::fetch( (int)$id );
            if ( !$object )
                continue;

            if ( !empty( $contentTypeIdentifiers ) && !in_array( $object->attribute( 'class_identifier' ), $contentTypeIdentifiers ) )
                continue;

            $items[] = $object;
            if ( $limit !== null && count( $items ) >= (int)$limit )
                break;
        }

        return $items;
    }

    /**
     * RelationService: load a single related Location from a relation field.
     */
    public static function loadFieldRelationLocation( $content, $fieldDefinitionIdentifier, $contentTypeIdentifiers = array() )
    {
        $object = self::loadFieldRelation( $content, $fieldDefinitionIdentifier, $contentTypeIdentifiers );
        return $object ? $object->mainNode() : false;
    }

    /**
     * RelationService: load all related Locations from a relation field.
     */
    public static function loadFieldRelationLocations( $content, $fieldDefinitionIdentifier, $contentTypeIdentifiers = array(), $limit = null )
    {
        $objects = self::loadFieldRelations( $content, $fieldDefinitionIdentifier, $contentTypeIdentifiers, $limit );
        $nodes = array();
        foreach ( $objects as $object )
        {
            $node = $object->mainNode();
            if ( $node )
                $nodes[] = $node;
        }
        return $nodes;
    }

    /**
     * RelationService: load reverse-related Content.
     */
    public static function loadReverseFieldRelations( $content, $fieldDefinitionIdentifier, $contentTypeIdentifiers = array(), $limit = null )
    {
        $objects = self::getReverseRelatedObjects( $content, $fieldDefinitionIdentifier );
        if ( empty( $contentTypeIdentifiers ) )
            $contentTypeIdentifiers = array();

        $items = array();
        foreach ( $objects as $object )
        {
            if ( !$object instanceof eZContentObject )
                continue;

            if ( !empty( $contentTypeIdentifiers ) && !in_array( $object->attribute( 'class_identifier' ), $contentTypeIdentifiers ) )
                continue;

            $items[] = $object;
            if ( $limit !== null && count( $items ) >= (int)$limit )
                break;
        }

        return $items;
    }

    /**
     * RelationService: load reverse-related Locations.
     */
    public static function loadReverseFieldRelationLocations( $content, $fieldDefinitionIdentifier, $contentTypeIdentifiers = array(), $limit = null )
    {
        $objects = self::loadReverseFieldRelations( $content, $fieldDefinitionIdentifier, $contentTypeIdentifiers, $limit );
        $nodes = array();
        foreach ( $objects as $object )
        {
            $node = $object->mainNode();
            if ( $node )
                $nodes[] = $node;
        }
        return $nodes;
    }

    /**
     * UrlGenerator: generate a URL for a Content or Location object.
     */
    public static function generateUrl( $object, $parameters = array(), $referenceType = self::ABSOLUTE_PATH )
    {
        $url = '';
        if ( is_object( $object ) && method_exists( $object, 'attribute' ) )
        {
            $url = $object->attribute( 'url_alias' );
        }
        elseif ( is_array( $object ) && isset( $object['url_alias'] ) )
        {
            $url = $object['url_alias'];
        }
        elseif ( is_numeric( $object ) )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$object );
            if ( $node )
                $url = $node->attribute( 'url_alias' );
        }

        if ( $referenceType === self::ABSOLUTE_URL )
        {
            $host = eZSys::hostname();
            $scheme = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            return $scheme . '://' . $host . $url;
        }

        if ( $referenceType === self::NETWORK_PATH )
        {
            return '//' . eZSys::hostname() . $url;
        }

        return $url;
    }

    /**
     * Settings: get all Site API settings as an array.
     */
    public static function getSettings()
    {
        $ini = eZINI::instance( 'site.ini' );
        $contentIni = eZINI::instance( 'content.ini' );

        $locale = eZLocale::currentLocaleCode();
        $rootNode = (int)$contentIni->variable( 'NodeSettings', 'RootNode' );

        return array(
            'prioritizedLanguages' => $locale ? array( $locale ) : array( 'eng-GB' ),
            'useAlwaysAvailable' => true,
            'rootLocationId' => $rootNode,
            'showHiddenItems' => false,
            'failOnMissingField' => false,
        );
    }

    /**
     * Settings: get a single setting value.
     */
    public static function getSetting( $name )
    {
        $settings = self::getSettings();
        return isset( $settings[$name] ) ? $settings[$name] : null;
    }

    /**
     * Site: return the class name that acts as all services in legacy mode.
     */
    public static function getFilterService() { return __CLASS__; }
    public static function getFindService() { return __CLASS__; }
    public static function getLoadService() { return __CLASS__; }
    public static function getRelationService() { return __CLASS__; }

    /**
     * Load all direct children of a location.
     */
    public static function filterChildren( $parentNodeId, array $options = array() )
    {
        $parentNodeId = (int)$parentNodeId;
        if ( $parentNodeId <= 0 )
            return array( 'total' => 0, 'items' => array() );

        $query = array(
            'parent_node_id' => $parentNodeId,
            'depth' => 1,
            'limit' => isset( $options['limit'] ) ? (int)$options['limit'] : 10,
            'offset' => isset( $options['offset'] ) ? (int)$options['offset'] : 0,
            'class_filter' => isset( $options['class_filter'] ) ? $options['class_filter'] : '',
            'sort' => isset( $options['sort'] ) ? $options['sort'] : 'published',
        );

        return self::filterLocations( $query );
    }

    /**
     * Full-text search helper.
     */
    public static function findContentText( $text, $limit = 10, $offset = 0 )
    {
        return self::findContent( array( 'full_text' => $text, 'limit' => $limit, 'offset' => $offset ) );
    }

    private static function runSearch( $query, $type )
    {
        $items = array();
        $total = 0;

        if ( isset( $query['content_id'] ) && (int)$query['content_id'] > 0 )
        {
            $content = eZContentObject::fetch( (int)$query['content_id'] );
            if ( $content )
                $items = array( $content );
            $total = count( $items );
        }
        elseif ( isset( $query['full_text'] ) && $query['full_text'] !== '' )
        {
            $search = eZSearch::search( (string)$query['full_text'] );
            $items = is_array( $search ) ? $search : ( is_object( $search ) && isset( $search->SearchResult ) ? $search->SearchResult : array() );
            $total = count( $items );
        }
        elseif ( isset( $query['parent_node_id'] ) && (int)$query['parent_node_id'] > 0 )
        {
            $treeParams = array(
                'Depth' => isset( $query['depth'] ) ? (int)$query['depth'] : 1,
                'Limit' => isset( $query['limit'] ) ? (int)$query['limit'] : 25,
                'Offset' => isset( $query['offset'] ) ? (int)$query['offset'] : 0,
                'SortBy' => self::parseSort( isset( $query['sort'] ) ? $query['sort'] : 'published' ),
            );

            $classFilter = isset( $query['class_filter'] ) ? $query['class_filter'] : '';
            if ( $classFilter !== '' )
            {
                $treeParams['ClassFilterType'] = 'include';
                $treeParams['ClassFilterArray'] = is_array( $classFilter ) ? $classFilter : array( $classFilter );
            }

            if ( isset( $query['attribute_filter'] ) && is_array( $query['attribute_filter'] ) && !empty( $query['attribute_filter'] ) )
            {
                $treeParams['AttributeFilter'] = $query['attribute_filter'];
            }

            $parentNodeId = (int)$query['parent_node_id'];
            $items = eZContentObjectTreeNode::subTreeByNodeID( $treeParams, $parentNodeId );
            if ( !is_array( $items ) )
                $items = array();

            if ( isset( $query['exclude_node_id'] ) && (int)$query['exclude_node_id'] > 0 )
            {
                $excluded = (int)$query['exclude_node_id'];
                $filtered = array();
                foreach ( $items as $item )
                {
                    if ( $item instanceof eZContentObjectTreeNode && (int)$item->attribute( 'node_id' ) === $excluded )
                        continue;
                    $filtered[] = $item;
                }
                $items = $filtered;
            }

            $total = count( $items );
        }

        return array(
            'total' => $total,
            'totalCount' => $total,
            'items' => $items,
            'searchHits' => $items,
        );
    }

    private static function parseSort( $sort )
    {
        if ( is_array( $sort ) && count( $sort ) >= 2 )
            return $sort;

        switch ( $sort )
        {
            case 'name':
                return array( 'name', true );
            case 'modified':
                return array( 'modified', false );
            case 'priority':
                return array( 'priority', false );
            case 'published':
            default:
                return array( 'published', false );
        }
    }

    public static function getContentObject( $content )
    {
        if ( $content instanceof eZContentObject )
            return $content;
        if ( $content instanceof eZContentObjectTreeNode )
            return $content->object();
        if ( is_numeric( $content ) )
            return eZContentObject::fetch( (int)$content );

        return false;
    }

    private static function getReverseRelatedObjects( $content, $fieldDefinitionIdentifier )
    {
        $contentObject = self::getContentObject( $content );
        if ( !$contentObject )
            return array();

        $attributeID = 0;
        if ( $fieldDefinitionIdentifier !== '' )
        {
            $class = eZContentClass::fetch( (int)$contentObject->attribute( 'contentclass_id' ) );
            if ( $class instanceof eZContentClass )
            {
                $classAttribute = $class->fetchAttributeByIdentifier( $fieldDefinitionIdentifier );
                if ( $classAttribute instanceof eZContentClassAttribute )
                    $attributeID = (int)$classAttribute->attribute( 'id' );
            }
        }

        $objects = $contentObject->reverseRelatedObjectList( false, $attributeID, false, array( 'AsObject' => true ) );
        return is_array( $objects ) ? $objects : array();
    }

    private static function extractRelationIds( $field )
    {
        $ids = array();
        if ( !is_object( $field ) )
            return $ids;

        $dataType = $field->attribute( 'data_type_string' );
        $content = $field->content();

        if ( $dataType === 'ezobjectrelation' )
        {
            if ( is_numeric( $content ) )
                $ids[] = (int)$content;
            elseif ( is_array( $content ) && isset( $content['contentobject_id'] ) )
                $ids[] = (int)$content['contentobject_id'];
            elseif ( is_object( $content ) && method_exists( $content, 'attribute' ) && $content->attribute( 'contentobject_id' ) )
                $ids[] = (int)$content->attribute( 'contentobject_id' );
        }
        elseif ( $dataType === 'ezobjectrelationlist' )
        {
            if ( is_array( $content ) )
            {
                foreach ( $content as $item )
                {
                    $id = 0;
                    if ( is_array( $item ) && isset( $item['contentobject_id'] ) )
                        $id = (int)$item['contentobject_id'];
                    elseif ( is_object( $item ) && method_exists( $item, 'attribute' ) )
                        $id = (int)$item->attribute( 'contentobject_id' );
                    elseif ( is_numeric( $item ) )
                        $id = (int)$item;

                    if ( $id > 0 )
                        $ids[] = $id;
                }
            }
        }

        return $ids;
    }
}
