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

    /**
     * The item view types each view type offers, where the settings limit them:
     * ViewTypeItemViewTypes[<view type>]=<item view type>,<item view type>,... of [BlockDefinition_<identifier>],
     * as Netgen Layouts lists item_view_types per view type. A view type without an entry offers all of
     * itemViewTypes(); one with a single item view type offers only that, and the editor hides its select.
     *
     * @param string $definitionIdentifier
     * @return array view type => item view types (identifiers, in the order given)
     */
    static function viewTypeItemViewTypes( $definitionIdentifier )
    {
        $ini = self::settings();
        $blockSection = 'BlockDefinition_' . self::currentIdentifier( $definitionIdentifier );
        if ( !$ini->hasVariable( $blockSection, 'ViewTypeItemViewTypes' ) )
            return array();

        $limits = array();
        foreach ( (array)$ini->variable( $blockSection, 'ViewTypeItemViewTypes' ) as $viewType => $list )
        {
            $itemViewTypes = array_values( array_unique( array_filter( array_map( 'trim', explode( ',', (string)$list ) ), 'strlen' ) ) );
            if ( !is_int( $viewType ) && (string)$viewType !== '' && !empty( $itemViewTypes ) )
                $limits[(string)$viewType] = $itemViewTypes;
        }
        return $limits;
    }

    /**
     * The parameters of a definition the editor does not offer: HiddenParameters[] of [BlockDefinition_<identifier>],
     * for a design whose templates do not read them (the image attribute and the thumbnail size of the gallery block
     * where the design draws its items itself). A hidden parameter keeps the value stored for it, or its default.
     *
     * @param string $definitionIdentifier
     * @return string[]
     */
    static function hiddenParameters( $definitionIdentifier )
    {
        $ini = self::settings();
        $blockSection = 'BlockDefinition_' . self::currentIdentifier( $definitionIdentifier );
        if ( !$ini->hasVariable( $blockSection, 'HiddenParameters' ) )
            return array();
        return array_values( array_unique( array_filter( array_map( 'trim', (array)$ini->variable( $blockSection, 'HiddenParameters' ) ), 'strlen' ) ) );
    }

    /**
     * The parameters a view type does not offer: ViewTypeHiddenParameters[<view type>]=<parameter>,<parameter>,... of
     * [BlockDefinition_<identifier>], as Netgen Layouts' valid_parameters per view type ('!number_of_thumbnails'), for
     * a design whose template of that view type does not read them (the starter's slider shows two images per slide
     * whatever "Thumbnails per row/slide" says). The editor hides them while that view type is selected; they keep
     * their stored value.
     *
     * @param string $definitionIdentifier
     * @return array view type => parameter names
     */
    static function viewTypeHiddenParameters( $definitionIdentifier )
    {
        $ini = self::settings();
        $blockSection = 'BlockDefinition_' . self::currentIdentifier( $definitionIdentifier );
        if ( !$ini->hasVariable( $blockSection, 'ViewTypeHiddenParameters' ) )
            return array();

        $hidden = array();
        foreach ( (array)$ini->variable( $blockSection, 'ViewTypeHiddenParameters' ) as $viewType => $list )
        {
            $names = array_values( array_unique( array_filter( array_map( 'trim', explode( ',', (string)$list ) ), 'strlen' ) ) );
            if ( !is_int( $viewType ) && (string)$viewType !== '' && !empty( $names ) )
                $hidden[(string)$viewType] = $names;
        }
        return $hidden;
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

    /**
     * The settings of a block definition: identifier, name, content_parameters, view_types, view_type_names,
     * item_view_types (itemViewTypes()), view_type_item_view_types (viewTypeItemViewTypes()), hidden_parameters
     * (hiddenParameters()), view_type_hidden_parameters (viewTypeHiddenParameters()), has_collection, is_container, placeholders and category; false when
     * explayouts.ini has no [BlockDefinition_<identifier>].
     *
     * @param string $definitionIdentifier
     * @return array|false
     */
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
        // HiddenViewTypes[]: view types a design does not draw, though explayouts or another extension lists them (a
        // reset line of ViewTypes[] does not help: explayouts' own settings are read after a siteaccess's)
        if ( $ini->hasVariable( $blockSection, 'HiddenViewTypes' ) )
        {
            $kept = array_values( array_diff( $viewTypes, array_map( 'trim', (array)$ini->variable( $blockSection, 'HiddenViewTypes' ) ) ) );
            if ( !empty( $kept ) )
                $viewTypes = $kept;
        }
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
            // item view type => label, as itemViewTypes() gives them (the handler's list and ItemViewTypes[])
            'item_view_types' => self::itemViewTypes( $definitionIdentifier ),
            // view type => its item view types, where ViewTypeItemViewTypes[] limits them (viewTypeItemViewTypes())
            'view_type_item_view_types' => self::viewTypeItemViewTypes( $definitionIdentifier ),
            // the parameters the editor does not offer (hiddenParameters()), and per view type (viewTypeHiddenParameters())
            'hidden_parameters' => self::hiddenParameters( $definitionIdentifier ),
            'view_type_hidden_parameters' => self::viewTypeHiddenParameters( $definitionIdentifier ),
            'has_collection' => $hasCollection,
            'is_container' => $isContainer,
            'placeholders' => $placeholders,
            'category' => $category,
        );
    }
}
