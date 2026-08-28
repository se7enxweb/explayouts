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
        $isContainer = $ini->hasVariable( $blockSection, 'IsContainer' ) ? (bool)$ini->variable( $blockSection, 'IsContainer' ) : false;
        $placeholders = $ini->hasVariable( $blockSection, 'Placeholders' ) ? $ini->variable( $blockSection, 'Placeholders' ) : array();
        $category = $ini->hasVariable( $blockSection, 'Category' ) ? $ini->variable( $blockSection, 'Category' ) : 'standard';

        return array(
            'identifier' => $definitionIdentifier,
            'name' => $name,
            'view_types' => $viewTypes,
            'has_collection' => $hasCollection,
            'is_container' => $isContainer,
            'placeholders' => $placeholders,
            'category' => $category,
        );
    }
}
