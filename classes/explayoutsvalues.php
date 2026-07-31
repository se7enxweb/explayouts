<?php

/**
 * Exponential 4 value object wrappers compatible with the Exp Site API Values pattern.
 *
 * These wrap native eZContentObject / eZContentObjectTreeNode objects and delegate
 * attribute access so they can be dropped into existing Smarty templates unchanged.
 */
class expLayoutsContent
{
    protected $object;
    protected $dataMap = null;

    public function __construct( $object )
    {
        $this->object = $object;
    }

    public function attribute( $name )
    {
        switch ( $name )
        {
            case 'id': return $this->getId();
            case 'name': return $this->getName();
            case 'remote_id': return $this->getRemoteId();
            case 'url_alias': return $this->getUrl();
            case 'class_identifier': return $this->object ? $this->object->attribute( 'class_identifier' ) : '';
        }

        return $this->object ? $this->object->attribute( $name ) : null;
    }

    public function hasAttribute( $name )
    {
        return $this->object && $this->object->hasAttribute( $name );
    }

    public function __get( $name )
    {
        return $this->attribute( $name );
    }

    public function __call( $name, $args )
    {
        if ( $this->object && method_exists( $this->object, $name ) )
            return call_user_func_array( array( $this->object, $name ), $args );

        return null;
    }

    public function getId()
    {
        return $this->object ? (int)$this->object->attribute( 'id' ) : 0;
    }

    public function getName()
    {
        return $this->object ? (string)$this->object->attribute( 'name' ) : '';
    }

    public function getRemoteId()
    {
        return $this->object ? (string)$this->object->attribute( 'remote_id' ) : '';
    }

    public function getUrl( $referenceType = expLayoutsSiteAPI::ABSOLUTE_PATH )
    {
        $node = $this->getMainLocation();
        if ( !$node )
            return '';

        $url = $node->attribute( 'url_alias' );
        if ( $referenceType === expLayoutsSiteAPI::ABSOLUTE_URL )
        {
            $scheme = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            return $scheme . '://' . eZSys::hostname() . $url;
        }
        if ( $referenceType === expLayoutsSiteAPI::NETWORK_PATH )
            return '//' . eZSys::hostname() . $url;

        return $url;
    }

    public function getContentInfo()
    {
        if ( !$this->object )
            return array();

        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'published' => $this->object->attribute( 'published' ),
            'modified' => $this->object->attribute( 'modified' ),
            'classIdentifier' => $this->object->attribute( 'class_identifier' ),
            'remoteId' => $this->getRemoteId(),
        );
    }

    public function getMainLocation()
    {
        return $this->object ? $this->object->mainNode() : false;
    }

    public function getLocations( $limit = 25, $offset = 0 )
    {
        if ( !$this->object )
            return array();

        $nodes = eZContentObjectTreeNode::fetchByContentObjectID( $this->getId(), true, $limit, $offset );
        return is_array( $nodes ) ? $nodes : array();
    }

    public function hasField( $identifier )
    {
        $map = $this->getDataMap();
        return isset( $map[$identifier] ) && is_object( $map[$identifier] );
    }

    public function getField( $identifier )
    {
        $map = $this->getDataMap();
        return isset( $map[$identifier] ) && is_object( $map[$identifier] ) ? $map[$identifier] : false;
    }

    public function getFieldValue( $identifier )
    {
        $field = $this->getField( $identifier );
        return $field ? $field->content() : null;
    }

    public function getFields()
    {
        return $this->getDataMap();
    }

    public function getObject()
    {
        return $this->object;
    }

    protected function getDataMap()
    {
        if ( $this->dataMap === null )
            $this->dataMap = $this->object ? $this->object->attribute( 'data_map' ) : array();

        return $this->dataMap;
    }
}

class expLayoutsLocation
{
    protected $node;

    public function __construct( $node )
    {
        $this->node = $node;
    }

    public function attribute( $name )
    {
        switch ( $name )
        {
            case 'id': return $this->getId();
            case 'name': return $this->getName();
            case 'url_alias': return $this->getUrl();
            case 'contentobject_id': return $this->getContentId();
            case 'remote_id': return $this->getRemoteId();
        }

        return $this->node ? $this->node->attribute( $name ) : null;
    }

    public function hasAttribute( $name )
    {
        return $this->node && $this->node->hasAttribute( $name );
    }

    public function __get( $name )
    {
        return $this->attribute( $name );
    }

    public function __call( $name, $args )
    {
        if ( $this->node && method_exists( $this->node, $name ) )
            return call_user_func_array( array( $this->node, $name ), $args );

        return null;
    }

    public function getId()
    {
        return $this->node ? (int)$this->node->attribute( 'node_id' ) : 0;
    }

    public function getName()
    {
        return $this->node ? (string)$this->node->attribute( 'name' ) : '';
    }

    public function getContentId()
    {
        return $this->node ? (int)$this->node->attribute( 'contentobject_id' ) : 0;
    }

    public function getRemoteId()
    {
        return $this->node ? (string)$this->node->attribute( 'remote_id' ) : '';
    }

    public function getUrl( $referenceType = expLayoutsSiteAPI::ABSOLUTE_PATH )
    {
        if ( !$this->node )
            return '';

        $url = $this->node->attribute( 'url_alias' );
        if ( $referenceType === expLayoutsSiteAPI::ABSOLUTE_URL )
        {
            $scheme = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            return $scheme . '://' . eZSys::hostname() . $url;
        }
        if ( $referenceType === expLayoutsSiteAPI::NETWORK_PATH )
            return '//' . eZSys::hostname() . $url;

        return $url;
    }

    public function getContent()
    {
        return $this->node ? $this->node->object() : false;
    }

    public function getContentInfo()
    {
        $content = $this->getContent();
        return $content ? ( new expLayoutsContent( $content ) )->getContentInfo() : array();
    }

    public function getParent()
    {
        return $this->node ? $this->node->attribute( 'parent' ) : false;
    }

    public function getChildren( $limit = 25, $offset = 0, array $options = array() )
    {
        if ( !$this->node )
            return array();

        $query = expLayoutsQueryType::children( $this->getId(), $options );
        $query['limit'] = (int)$limit;
        $query['offset'] = (int)$offset;

        return expLayoutsSiteAPI::filterLocations( $query );
    }

    public function getSiblings( $limit = 25, $offset = 0, array $options = array() )
    {
        $options['limit'] = (int)$limit;
        $options['offset'] = (int)$offset;
        return expLayoutsSiteAPI::filterLocations( expLayoutsQueryType::siblings( $this->getId(), $options ) );
    }

    public function getSubtree( $depth = 10, $limit = 25, $offset = 0, array $options = array() )
    {
        $options['depth'] = (int)$depth;
        $options['limit'] = (int)$limit;
        $options['offset'] = (int)$offset;
        return expLayoutsSiteAPI::filterLocations( expLayoutsQueryType::subtree( $this->getId(), $options ) );
    }

    public function getPath()
    {
        return $this->node ? $this->node->attribute( 'path' ) : array();
    }

    public function getNode()
    {
        return $this->node;
    }
}
