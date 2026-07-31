<?php

class expLayoutsExpSiteApi
{
    public static function loadContent( $contentId )
    {
        return eZContentObject::fetch( (int)$contentId );
    }

    public static function loadLocation( $nodeId )
    {
        return eZContentObjectTreeNode::fetch( (int)$nodeId );
    }

    public static function convertContent( eZContentObject $object )
    {
        $node = $object->mainNode();
        return array(
            'id' => (int)$object->attribute( 'id' ),
            'name' => (string)$object->attribute( 'name' ),
            'class_identifier' => (string)$object->attribute( 'class_identifier' ),
            'main_node_id' => $node instanceof eZContentObjectTreeNode ? (int)$node->attribute( 'node_id' ) : 0,
        );
    }

    public static function convertLocation( eZContentObjectTreeNode $node )
    {
        $object = $node->attribute( 'object' );
        return array(
            'id' => (int)$node->attribute( 'node_id' ),
            'content_id' => (int)$node->attribute( 'contentobject_id' ),
            'name' => (string)$node->attribute( 'name' ),
            'url_alias' => (string)$node->attribute( 'url_alias' ),
            'path_string' => (string)$node->attribute( 'path_string' ),
            'class_identifier' => $object instanceof eZContentObject ? (string)$object->attribute( 'class_identifier' ) : '',
        );
    }

    public static function currentNode()
    {
        $module = eZModule::currentModule();
        if ( !$module instanceof eZModule )
            return false;

        $ini = eZINI::instance();
        $root = (int)$ini->variable( 'ContentSettings', 'RootNode' );
        $nodeId = $module->hasAction( 'NodeID' ) ? $module->action( 'NodeID' ) : $root;
        return self::loadLocation( (int)$nodeId );
    }

    public static function currentObject()
    {
        $node = self::currentNode();
        if ( !$node instanceof eZContentObjectTreeNode )
            return false;

        return $node->attribute( 'object' );
    }

    public static function convertContentToItem( $object )
    {
        if ( !$object instanceof eZContentObject )
            return array( 'id' => 0, 'content_id' => 0, 'name' => '', 'class_identifier' => '', 'location' => false, 'url' => '' );

        $node = $object->mainNode();
        return array(
            'id' => (int)$object->attribute( 'id' ),
            'content_id' => (int)$object->attribute( 'id' ),
            'name' => (string)$object->attribute( 'name' ),
            'class_identifier' => (string)$object->attribute( 'class_identifier' ),
            'location' => $node instanceof eZContentObjectTreeNode ? $node : false,
            'url' => $node instanceof eZContentObjectTreeNode ? (string)$node->attribute( 'url_alias' ) : '',
        );
    }

    public static function convertLocationToItem( $node )
    {
        if ( !$node instanceof eZContentObjectTreeNode )
            return array( 'id' => 0, 'content_id' => 0, 'name' => '', 'class_identifier' => '', 'location' => false, 'url' => '' );

        $object = $node->attribute( 'object' );
        return array(
            'id' => (int)$node->attribute( 'contentobject_id' ),
            'content_id' => (int)$node->attribute( 'contentobject_id' ),
            'name' => (string)$node->attribute( 'name' ),
            'class_identifier' => $object instanceof eZContentObject ? (string)$object->attribute( 'class_identifier' ) : '',
            'location' => $node,
            'url' => (string)$node->attribute( 'url_alias' ),
        );
    }
}