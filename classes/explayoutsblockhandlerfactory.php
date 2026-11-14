<?php
class expLayoutsBlockHandlerFactory
{
    /** @var eZINI|null explayouts.ini of the siteaccess useSiteAccess() chose, null for the current siteaccess */
    private static $settings = null;

    /**
     * Reads the block definitions from the settings of another siteaccess for the rest of the request: the layout
     * editor runs in the administration, but the blocks it edits are drawn by a site, and a design keeps the block
     * settings it adds (ViewTypes[], ItemViewTypes[], AvailableBlocks[] in settings/siteaccess/<site>) where only that
     * site reads them. The editor calls this with the siteaccess it previews in (expLayoutsPreviewSiteAccess), so what
     * it offers, accepts and previews are the same. false or '' goes back to the current siteaccess.
     *
     * @param string|false $siteAccess
     * @return string the siteaccess the settings come from ('' for the current one)
     */
    static function useSiteAccess( $siteAccess )
    {
        self::$settings = null;
        if ( !is_string( $siteAccess ) || $siteAccess === '' )
            return '';

        $current = eZSiteAccess::current();
        if ( is_array( $current ) && isset( $current['name'] ) && $current['name'] === $siteAccess )
            return '';

        self::$settings = eZSiteAccess::getIni( $siteAccess, 'explayouts.ini' );
        return $siteAccess;
    }

    /**
     * explayouts.ini the block definitions are read from: the current siteaccess's, or the one useSiteAccess() chose.
     *
     * @return eZINI
     */
    static function settings()
    {
        return self::$settings instanceof eZINI ? self::$settings : eZINI::instance( 'explayouts.ini' );
    }

    /**
     * Renamed block identifiers: the content component blocks were
     * ibexa_component_<type> and are exp_component_<type>. A block stored
     * under the old name still finds its definition, until
     * bin/php/updatecomponentblockidentifiers.php has renamed the rows.
     */
    static function currentIdentifier( $definitionIdentifier )
    {
        $definitionIdentifier = (string)$definitionIdentifier;
        if ( strpos( $definitionIdentifier, 'ibexa_component_' ) === 0 )
            return 'exp_component_' . substr( $definitionIdentifier, strlen( 'ibexa_component_' ) );

        return $definitionIdentifier;
    }

    static function get( $definitionIdentifier )
    {
        $ini = self::settings();
        $blockSection = 'BlockDefinition_' . self::currentIdentifier( $definitionIdentifier );
        if ( !$ini->hasGroup( $blockSection ) )
            return false;

        $handlerClass = $ini->variable( $blockSection, 'Handler' );
        if ( !class_exists( $handlerClass ) )
            return false;

        return new $handlerClass();
    }

    /**
     * The item view types editors can choose for a block of the definition, as identifier => label: the handler's
     * own list (getItemViewTypes(), when it has one), then the ones the settings add in [BlockDefinition_<identifier>]
     * with ItemViewTypes[<item view type>]=<label> (ItemViewTypes[]=<item view type> is read as its own label). A
     * configured entry replaces the handler's label of the same identifier.
     *
     * Item view types are an axis of their own: every view type of the definition offers all of them, and whether a
     * design draws a combination is up to the design. A definition whose handler has no list and whose settings add
     * none has no item view type (an empty array), as before.
     *
     * @param string $definitionIdentifier
     * @return array item view type => label
     */
    static function itemViewTypes( $definitionIdentifier )
    {
        $itemViewTypes = array();
        $handler = self::get( $definitionIdentifier );
        if ( is_object( $handler ) && method_exists( $handler, 'getItemViewTypes' ) )
        {
            foreach ( (array)$handler->getItemViewTypes() as $itemViewType => $label )
                $itemViewTypes[(string)$itemViewType] = (string)$label;
        }

        $ini = self::settings();
        $blockSection = 'BlockDefinition_' . self::currentIdentifier( $definitionIdentifier );
        if ( $ini->hasVariable( $blockSection, 'ItemViewTypes' ) )
        {
            $configured = $ini->variable( $blockSection, 'ItemViewTypes' );
            foreach ( is_array( $configured ) ? $configured : array() as $itemViewType => $label )
            {
                if ( is_int( $itemViewType ) )
                    $itemViewType = $label;
                if ( (string)$itemViewType === '' )
                    continue;
                $itemViewTypes[(string)$itemViewType] = (string)$label;
            }
        }

        return $itemViewTypes;
    }

    static function getAvailableBlocks()
    {
        $ini = self::settings();
        if ( !$ini->hasVariable( 'BlockSettings', 'AvailableBlocks' ) )
            return array();

        // an extension may list a block explayouts lists too: offered once
        return array_values( array_unique( (array)$ini->variable( 'BlockSettings', 'AvailableBlocks' ) ) );
    }

    /**
     * The group of a parameter of a block definition: 'content' when [BlockDefinition_<identifier>]
     * ContentParameters[] lists it, else 'design'. A parameter inside a compound one (a child) is in the group of its
     * parent.
     *
     * @param string $definitionIdentifier
     * @param string $parameterName the name of a top-level parameter
     * @return string 'content' or 'design'
     */
    static function parameterGroup( $definitionIdentifier, $parameterName )
    {
        $info = self::getBlockInfo( $definitionIdentifier );
        return is_array( $info ) && in_array( (string)$parameterName, $info['content_parameters'], true ) ? 'content' : 'design';
    }

    static function getBlockInfo( $definitionIdentifier )
    {
        $ini = self::settings();
        $blockSection = 'BlockDefinition_' . self::currentIdentifier( $definitionIdentifier );
        if ( !$ini->hasGroup( $blockSection ) )
            return false;

        $name = $ini->hasVariable( $blockSection, 'Name' ) ? $ini->variable( $blockSection, 'Name' ) : $definitionIdentifier;
        // a design may list a view type explayouts lists too (an ini file appends to ViewTypes[] without removing
        // repeats): offered once, as getAvailableBlocks() does with the blocks
        $viewTypes = $ini->hasVariable( $blockSection, 'ViewTypes' ) ? array_values( array_unique( (array)$ini->variable( $blockSection, 'ViewTypes' ) ) ) : array( 'default' );
        $hasCollection = $ini->hasVariable( $blockSection, 'HasCollection' ) ? (bool)$ini->variable( $blockSection, 'HasCollection' ) : false;
        $isContainer = $ini->hasVariable( $blockSection, 'IsContainer' ) ? (bool)$ini->variable( $blockSection, 'IsContainer' ) : false;
        $placeholders = $ini->hasVariable( $blockSection, 'Placeholders' ) ? $ini->variable( $blockSection, 'Placeholders' ) : array();
        $category = $ini->hasVariable( $blockSection, 'Category' ) ? $ini->variable( $blockSection, 'Category' ) : 'standard';
        // Labels of the view types in the editor's View type select, as
        // ViewTypeNames[<view type>]=<label>; a view type without one is shown
        // by its identifier.
        $viewTypeNames = $ini->hasVariable( $blockSection, 'ViewTypeNames' ) ? $ini->variable( $blockSection, 'ViewTypeNames' ) : array();
        if ( !is_array( $viewTypeNames ) )
            $viewTypeNames = array();

        // The parameters edited in the Content tab of the layout editor, as the reference's parameters of the group
        // "content"; the others are in the Design tab (and every parameter of a definition that lists none).
        $contentParameters = $ini->hasVariable( $blockSection, 'ContentParameters' ) ? $ini->variable( $blockSection, 'ContentParameters' ) : array();
        if ( !is_array( $contentParameters ) )
            $contentParameters = array();

        return array(
            'identifier' => $definitionIdentifier,
            'name' => $name,
            'content_parameters' => array_values( array_filter( array_map( 'trim', $contentParameters ), 'strlen' ) ),
            'view_types' => $viewTypes,
            'view_type_names' => $viewTypeNames,
            'has_collection' => $hasCollection,
            'is_container' => $isContainer,
            'placeholders' => $placeholders,
            'category' => $category,
        );
    }
}
