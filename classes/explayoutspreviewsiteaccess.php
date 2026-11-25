<?php
/**
 * The public siteaccess an administration renders layouts in: the layout preview (explayouts_ui/layout_preview) and
 * the Load more of a list block called from the administration (expLayoutsAjaxLoadMoreServer).
 *
 * It was site.ini [SiteSettings] DefaultAccess. In an installation that serves several sites from one tree that is
 * the default of the whole installation, not the site of the administration in use: the preview of one site's
 * layout ran with another site's settings, design and var directory, while the database connection stayed the one
 * of the administration (two installations mixed in one request, and templates that do not exist there).
 *
 * In this order, the first that qualifies:
 *
 *   1. explayouts.ini [PreviewSettings] SiteAccess of the administration (when set, it must qualify, or nothing does)
 *   2. site.ini [SiteSettings] FrontSiteAccess of the administration
 *   3. site.ini [SiteAccessSettings] RelatedSiteAccessList[] of the administration: the first that is not the
 *      administration itself and not an administration (a SiteDesign beginning with "admin")
 *   4. site.ini [SiteSettings] DefaultAccess
 *
 * A siteaccess qualifies when it is not the current one, is not an administration, and uses the same database
 * (DatabaseSettings Server, Port, Database and DatabaseImplementation): the preview switches settings and design, not
 * the database connection. None qualifies: false, and the caller says so instead of rendering.
 *
 * That is one siteaccess for the whole administration. Where the layout is known (the layout editor, the layout
 * preview), resolveForLayout() first asks the layout's rules which site it belongs to, and falls back to resolve().
 *
 * @license GPL-2.0-or-later
 */
class expLayoutsPreviewSiteAccess
{
    /** The database settings that must be the same for the preview siteaccess */
    const DATABASE_KEYS = array( 'DatabaseImplementation', 'Server', 'Port', 'Database' );

    /** Why resolve() found nothing, for the message of the caller */
    public static $lastError = '';

    /** AvailableSiteAccessList[] as read from the settings; set in place of them (tests) */
    public static $availableSiteAccesses = null;

    /** layout id => rules as siteAccessesOfRules() reads them; set in place of the database (tests) */
    public static $layoutRules = null;

    /** node id => the node ids of its path; set in place of the database (tests) */
    public static $nodePaths = array();

    /** layout id of a draft => id of its published layout; set in place of the database (tests) */
    public static $publishedLayouts = null;

    /**
     * @return string|false the name of the siteaccess, or false (see $lastError)
     */
    public static function resolve()
    {
        $current = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '';
        $siteIni = eZINI::instance( 'site.ini' );
        $layoutsIni = eZINI::instance( 'explayouts.ini' );

        $configured = $layoutsIni->hasVariable( 'PreviewSettings', 'SiteAccess' )
            ? trim( (string)$layoutsIni->variable( 'PreviewSettings', 'SiteAccess' ) ) : '';

        $candidates = array();
        if ( $configured === '' )
        {
            if ( $siteIni->hasVariable( 'SiteSettings', 'FrontSiteAccess' ) )
                $candidates[] = $siteIni->variable( 'SiteSettings', 'FrontSiteAccess' );
            if ( $siteIni->hasVariable( 'SiteAccessSettings', 'RelatedSiteAccessList' ) )
                $candidates = array_merge( $candidates, (array)$siteIni->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' ) );
            if ( $siteIni->hasVariable( 'SiteSettings', 'DefaultAccess' ) )
                $candidates[] = $siteIni->variable( 'SiteSettings', 'DefaultAccess' );
        }
        else
        {
            $candidates[] = $configured;
        }

        $currentDatabase = self::databaseOf( $siteIni );
        return self::choose( $candidates, $current, $currentDatabase, array( __CLASS__, 'describe' ), $configured !== '' );
    }

    /**
     * The public siteaccess a layout is shown in, for the layout editor and the layout preview. One preview
     * siteaccess per administration (resolve()) showed the layouts of every site the administration serves with the
     * settings and design of one site, so the editor did not offer the view types of the others
     * (se7enxweb/exponential#364).
     *
     * In this order, the first that qualifies (see choose()):
     *
     *   1. the siteaccesses named by the siteaccess conditions of the layout's enabled rules
     *   2. without such a condition, for the node and subtree targets of those rules: the public siteaccesses whose
     *      content.ini RootNode lies deepest above the node
     *   3. resolve(): the preview siteaccess of the administration (a layout without rules, a new one, gets it)
     *
     * Of several in 1 or 2 (a site in two languages on one root node), the one whose ContentObjectLocale is
     * $language comes first, then the order they were found in.
     *
     * @param int $layoutId
     * @param string|null $language a locale such as ger-DE; null: the ContentObjectLocale of the current siteaccess
     * @return string|false the name of the siteaccess, or false (see $lastError)
     */
    public static function resolveForLayout( $layoutId, $language = null )
    {
        $found = self::siteAccessesOfRules( self::layoutRules( (int)$layoutId ), array( __CLASS__, 'siteAccessesForNode' ) );
        if ( !empty( $found ) )
        {
            if ( $language === null )
            {
                $siteIni = eZINI::instance( 'site.ini' );
                $language = $siteIni->hasVariable( 'RegionalSettings', 'ContentObjectLocale' )
                    ? (string)$siteIni->variable( 'RegionalSettings', 'ContentObjectLocale' ) : '';
            }
            $found = self::byLanguage( $found, (string)$language, function( $name ) {
                $ini = eZSiteAccess::getIni( $name, 'site.ini' );
                return $ini->hasVariable( 'RegionalSettings', 'ContentObjectLocale' )
                    ? (string)$ini->variable( 'RegionalSettings', 'ContentObjectLocale' ) : '';
            } );
            $current = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '';
            $siteAccess = self::choose( $found, $current, self::databaseOf( eZINI::instance( 'site.ini' ) ), array( __CLASS__, 'describe' ) );
            if ( $siteAccess !== false )
                return $siteAccess;
        }
        return self::resolve();
    }

    /**
     * The siteaccesses the rules of a layout name (steps 1 and 2 of resolveForLayout()), without reading anything
     * (testable on its own).
     *
     * @param array $rules each array( 'conditions' => array( array( type, value ) ), 'targets' => array( array( type, value ) ) ),
     *                     in the order of the rules
     * @param callable $nodeSiteAccesses node id => names (siteAccessesForNode())
     * @return string[] names in the order found, each once
     */
    public static function siteAccessesOfRules( array $rules, $nodeSiteAccesses )
    {
        $named = array();
        foreach ( $rules as $rule )
        {
            foreach ( isset( $rule['conditions'] ) ? $rule['conditions'] : array() as $condition )
            {
                if ( $condition[0] !== 'siteaccess' )
                    continue;
                // as the resolver reads it: a JSON list, or one name
                $names = json_decode( (string)$condition[1], true );
                foreach ( is_array( $names ) ? $names : array( (string)$condition[1] ) as $name )
                    $named[] = trim( (string)$name );
            }
        }
        if ( empty( $named ) )
        {
            foreach ( $rules as $rule )
            {
                foreach ( isset( $rule['targets'] ) ? $rule['targets'] : array() as $target )
                {
                    if ( ( $target[0] === 'node' || $target[0] === 'subtree' ) && (int)$target[1] > 0 )
                        $named = array_merge( $named, (array)call_user_func( $nodeSiteAccesses, (int)$target[1] ) );
                }
            }
        }
        return array_values( array_unique( array_filter( $named, 'strlen' ) ) );
    }

    /**
     * The names with the ones whose locale is $language first, otherwise in their order.
     *
     * @param string[] $names
     * @param string $language
     * @param callable $localeOf name => locale
     * @return string[]
     */
    public static function byLanguage( array $names, $language, $localeOf )
    {
        if ( $language === '' )
            return $names;
        $first = $rest = array();
        foreach ( $names as $name )
        {
            if ( call_user_func( $localeOf, $name ) === $language )
                $first[] = $name;
            else
                $rest[] = $name;
        }
        return array_merge( $first, $rest );
    }

    /**
     * The public siteaccesses a node is shown in: of AvailableSiteAccessList[] (administrations and siteaccesses that
     * require a login left out), the ones whose content.ini RootNode lies deepest above the node; several when they
     * share that root node.
     *
     * @param int $nodeId
     * @return string[]
     */
    public static function siteAccessesForNode( $nodeId )
    {
        $path = isset( self::$nodePaths[$nodeId] ) ? self::$nodePaths[$nodeId] : null;
        if ( $path === null )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$nodeId );
            if ( !$node instanceof eZContentObjectTreeNode )
                return array();
            $path = array_map( 'intval', array_values( array_filter( explode( '/', (string)$node->attribute( 'path_string' ) ) ) ) );
        }
        $roots = array();
        foreach ( (array)self::availableSiteAccesses() as $name )
        {
            // not a site visitors see: an administration, or a siteaccess that asks everyone to log in (an editorial
            // interface whose design is not called admin*, on the root node of the whole tree)
            $info = self::describe( $name );
            if ( !empty( $info['admin'] )
                 || eZSiteAccess::getIni( $name, 'site.ini' )->variable( 'SiteAccessSettings', 'RequireUserLogin' ) === 'true' )
                continue;
            $contentIni = eZSiteAccess::getIni( $name, 'content.ini' );
            $roots[$name] = $contentIni->hasVariable( 'NodeSettings', 'RootNode' ) ? (int)$contentIni->variable( 'NodeSettings', 'RootNode' ) : 2;
        }
        return self::deepestRoots( $path, $roots );
    }

    /**
     * The names whose root node lies deepest in $path (testable on its own).
     *
     * @param int[] $path the node ids from the top of the tree down to the node
     * @param array $roots name => root node id
     * @return string[]
     */
    public static function deepestRoots( array $path, array $roots )
    {
        $best = array();
        $bestDepth = -1;
        foreach ( $roots as $name => $rootNode )
        {
            $depth = array_search( (int)$rootNode, $path, true );
            if ( $depth === false || $depth < $bestDepth )
                continue;
            if ( $depth > $bestDepth )
            {
                $best = array();
                $bestDepth = $depth;
            }
            $best[] = (string)$name;
        }
        return $best;
    }

    /**
     * Whether a siteaccess is an administration (its SiteDesign begins with "admin") and the database it uses.
     *
     * @param string $name
     * @return array array( 'admin' => bool, 'database' => array )
     */
    public static function describe( $name )
    {
        $ini = eZSiteAccess::getIni( $name, 'site.ini' );
        return array(
            'admin' => strpos( (string)$ini->variable( 'DesignSettings', 'SiteDesign' ), 'admin' ) === 0,
            'database' => self::databaseOf( $ini ),
        );
    }

    /**
     * The enabled rules of a layout as siteAccessesOfRules() reads them, highest priority first.
     *
     * @param int $layoutId
     * @return array
     */
    protected static function layoutRules( $layoutId )
    {
        // The editor works on the draft, a layout of its own (status 1) with the identifier of the published one
        // (status 2), and the rules map the published one: a draft takes the rules of its published layout.
        $ids = array( $layoutId );
        $published = self::publishedLayoutId( $layoutId );
        if ( $published > 0 && $published !== $layoutId )
            $ids[] = $published;

        if ( self::$layoutRules !== null )
        {
            $rules = array();
            foreach ( $ids as $id )
                $rules = array_merge( $rules, isset( self::$layoutRules[$id] ) ? self::$layoutRules[$id] : array() );
            return $rules;
        }
        $rules = array();
        if ( $layoutId <= 0 || !class_exists( 'expLayoutsRule' ) )
            return $rules;
        $objects = eZPersistentObject::fetchObjectList( expLayoutsRule::definition(), null,
            array( 'layout_id' => array( $ids ), 'enabled' => 1 ), array( 'priority' => 'desc', 'id' => 'desc' ), null, true );
        foreach ( (array)$objects as $rule )
        {
            $entry = array( 'conditions' => array(), 'targets' => array() );
            foreach ( (array)$rule->conditions() as $condition )
                $entry['conditions'][] = array( (string)$condition->attribute( 'condition_type' ), (string)$condition->attribute( 'condition_value' ) );
            foreach ( (array)$rule->targets() as $target )
                $entry['targets'][] = array( (string)$target->attribute( 'target_type' ), (string)$target->attribute( 'target_value' ) );
            $rules[] = $entry;
        }
        return $rules;
    }

    /**
     * The id of the published layout of a draft (the layout with its identifier and status 2), as explayouts_core's
     * layout service finds it; the id itself for a published layout, 0 when there is none.
     *
     * @param int $layoutId
     * @return int
     */
    protected static function publishedLayoutId( $layoutId )
    {
        if ( self::$publishedLayouts !== null )
            return isset( self::$publishedLayouts[$layoutId] ) ? (int)self::$publishedLayouts[$layoutId] : 0;
        if ( $layoutId <= 0 || !class_exists( 'expLayoutsLayout' ) )
            return 0;
        $layout = expLayoutsLayout::fetch( $layoutId );
        if ( !is_object( $layout ) )
            return 0;
        if ( (int)$layout->attribute( 'status' ) === 2 )
            return $layoutId;
        $published = expLayoutsLayout::fetchByIdentifier( (string)$layout->attribute( 'identifier' ), 2 );
        return is_object( $published ) ? (int)$published->attribute( 'id' ) : 0;
    }

    /**
     * The choice itself, without reading settings (testable on its own).
     *
     * @param array $candidates names in the order of preference
     * @param string $current the current siteaccess
     * @param array $currentDatabase see databaseOf()
     * @param callable $describe name => array( 'admin' => bool, 'database' => array )
     * @param bool $configured whether the one candidate is the configured one
     * @return string|false
     */
    public static function choose( array $candidates, $current, array $currentDatabase, $describe, $configured = false )
    {
        self::$lastError = '';
        $available = self::availableSiteAccesses();
        $seen = array();
        foreach ( $candidates as $name )
        {
            $name = is_scalar( $name ) ? trim( (string)$name ) : '';
            if ( $name === '' || $name === $current || isset( $seen[$name] ) )
                continue;
            $seen[$name] = true;
            if ( $available !== null && !in_array( $name, $available, true ) )
            {
                self::$lastError = "The siteaccess '$name' is not in AvailableSiteAccessList.";
                continue;
            }
            $info = call_user_func( $describe, $name );
            if ( !empty( $info['admin'] ) )
            {
                self::$lastError = "The siteaccess '$name' is an administration.";
                continue;
            }
            if ( $info['database'] !== $currentDatabase )
            {
                self::$lastError = "The siteaccess '$name' uses another database than '$current'.";
                continue;
            }
            self::$lastError = '';
            return $name;
        }
        if ( self::$lastError === '' )
            self::$lastError = $configured
                ? 'explayouts.ini [PreviewSettings] SiteAccess names the current siteaccess.'
                : "No public siteaccess found for '$current' (explayouts.ini [PreviewSettings] SiteAccess).";
        return false;
    }

    /**
     * @param eZINI $ini a site.ini
     * @return array the database settings of DATABASE_KEYS
     */
    public static function databaseOf( $ini )
    {
        $database = array();
        foreach ( self::DATABASE_KEYS as $key )
            $database[$key] = $ini->hasVariable( 'DatabaseSettings', $key ) ? (string)$ini->variable( 'DatabaseSettings', $key ) : '';
        return $database;
    }

    /**
     * AvailableSiteAccessList[] without siteaccess overrides (as eZSiteAccess::match() reads it).
     *
     * @return string[]
     */
    protected static function availableSiteAccesses()
    {
        if ( self::$availableSiteAccesses !== null )
            return self::$availableSiteAccesses;
        $global = new eZINI( 'site.ini', 'settings', null, null, true );
        return array_map( 'strval', (array)$global->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) );
    }
}
