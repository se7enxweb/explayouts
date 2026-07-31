<?php
class expLayoutsBlockHandlerFactory
{
    static function get( $definitionIdentifier )
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        $blockSection = 'BlockDefinition_' . $definitionIdentifier;
        if ( !$ini->hasGroup( $blockSection ) )
            return false;

        $handlerClass = $ini->variable( $blockSection, 'Handler' );
        if ( !class_exists( $handlerClass ) )
            return false;

        return new $handlerClass();
    }

    static function getAvailableBlocks()
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        if ( !$ini->hasVariable( 'BlockSettings', 'AvailableBlocks' ) )
            return array();

        return $ini->variable( 'BlockSettings', 'AvailableBlocks' );
    }

    static function getBlockInfo( $definitionIdentifier )
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        $blockSection = 'BlockDefinition_' . $definitionIdentifier;
        if ( !$ini->hasGroup( $blockSection ) )
            return false;

        $name = $ini->hasVariable( $blockSection, 'Name' ) ? $ini->variable( $blockSection, 'Name' ) : $definitionIdentifier;
        $viewTypes = $ini->hasVariable( $blockSection, 'ViewTypes' ) ? $ini->variable( $blockSection, 'ViewTypes' ) : array( 'default' );
        $hasCollection = $ini->hasVariable( $blockSection, 'HasCollection' ) ? (bool)$ini->variable( $blockSection, 'HasCollection' ) : false;

        return array(
            'identifier' => $definitionIdentifier,
            'name' => $name,
            'view_types' => $viewTypes,
            'has_collection' => $hasCollection,
        );
    }
}
