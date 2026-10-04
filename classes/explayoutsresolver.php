<?php
class expLayoutsResolver
{
    private static $resolvedCache = array();

    static function resolve( $path = false )
    {
        if ( $path === false )
        {
            $uri = eZSys::requestURI();
            if ( ( $uri === '' || $uri === null ) && isset( $_SERVER['REQUEST_URI'] ) )
            {
                $uri = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
            }
            $path = $uri;
        }

        if ( $path === null )
            $path = '';

        $path = ltrim( $path, '/' );
        if ( stripos( $path, 'index.php/' ) === 0 )
            $path = substr( $path, 10 );
        if ( $path === '' )
            $path = 'home';

        $current = eZSiteAccess::current();
        $siteAccessName = ( is_array( $current ) && isset( $current['name'] ) ) ? $current['name'] : 'default';

        $cached = self::readCache( $path, $siteAccessName );
        if ( $cached !== false )
        {
            if ( (int)$cached['layout_id'] > 0 )
            {
                $layout = expLayoutsLayout::fetch( $cached['layout_id'] );
                if ( $layout && (int)$layout->attribute( 'status' ) === 2 && !expLayoutsLayoutType::isAdminType( $layout->attribute( 'layout_type' ) ) )
                {
                    eZDebug::writeNotice( "Cache hit for path '$path', layout=" . $layout->attribute( 'identifier' ), 'expLayoutsResolver' );
                    return $layout;
                }
            }
            else
            {
                eZDebug::writeNotice( "Cache hit for path '$path', no match", 'expLayoutsResolver' );
                return false;
            }
        }

        $rules = expLayoutsRule::fetchEnabled();
        foreach ( $rules as $rule )
        {
            if ( self::ruleMatches( $rule, $path ) )
            {
                $layout = expLayoutsLayout::fetch( $rule->attribute( 'layout_id' ) );
                // Admin layouts never reach the public site.
                if ( $layout && (int)$layout->attribute( 'status' ) === 2 && !expLayoutsLayoutType::isAdminType( $layout->attribute( 'layout_type' ) ) )
                {
                    self::writeCache( $path, $siteAccessName, (int)$rule->attribute( 'id' ), (int)$layout->attribute( 'id' ) );
                    eZDebug::writeNotice( 'Matched rule ' . $rule->attribute( 'id' ) . " for path '$path', layout=" . $layout->attribute( 'identifier' ), 'expLayoutsResolver' );
                    return $layout;
                }
            }
        }
        eZDebug::writeNotice( "No rule matched for path '$path'", 'expLayoutsResolver' );

        $ini = eZINI::instance( 'explayouts.ini' );
        $default = $ini->variable( 'ResolverSettings', 'DefaultLayout' );
        if ( $default )
        {
            $layout = expLayoutsLayout::fetchByIdentifier( $default, 2 );
            if ( $layout && !expLayoutsLayoutType::isAdminType( $layout->attribute( 'layout_type' ) ) )
            {
                self::writeCache( $path, $siteAccessName, 0, (int)$layout->attribute( 'id' ) );
                return $layout;
            }
        }

        self::writeCache( $path, $siteAccessName, 0, 0 );
        return false;
    }

    private static function cacheKey( $path, $siteAccessName )
    {
        return md5( $path . '|' . $siteAccessName );
    }

    private static function cacheDir()
    {
        $varDir = eZINI::instance( 'site.ini' )->variable( 'FileSettings', 'VarDir' );
        return $varDir . '/cache/explayouts/resolver';
    }

    private static function cacheFile( $key )
    {
        return self::cacheDir() . '/' . $key . '.php';
    }

    private static function readCache( $path, $siteAccessName )
    {
        $key = self::cacheKey( $path, $siteAccessName );

        if ( array_key_exists( $key, self::$resolvedCache ) )
        {
            return self::$resolvedCache[$key];
        }

        $file = self::cacheFile( $key );
        if ( file_exists( $file ) )
        {
            $data = @include( $file );
            if ( is_array( $data ) && isset( $data['expires'] ) && $data['expires'] > time() )
            {
                self::$resolvedCache[$key] = $data;
                return $data;
            }
            @unlink( $file );
        }

        return false;
    }

    /**
     * Whether the database answered the lookups just made.
     *
     * A failed query reads as "no rows", which reads as "no rule matched".
     * Cached, that turned a passing database fault into pages rendered
     * without their layout for the whole cache lifetime, on every web server
     * sharing var/: on 2026-09-24 a broken connection wrote "layout 0" for
     * /healthy-eating, /workout and others, and they stayed blank after the
     * database was fine again.
     */
    private static function databaseAnswered()
    {
        $db = eZDB::instance();
        if ( !$db || !$db->isConnected() )
            return false;
        return (int)$db->errorNumber() === 0;
    }

    private static function writeCache( $path, $siteAccessName, $ruleId, $layoutId )
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        $ttl = (int)$ini->variable( 'ResolverSettings', 'CacheTTL' );
        if ( $ttl <= 0 )
            $ttl = 3600;

        // "No rule matched" and the default layout are both what a failed
        // lookup looks like. Not remembered unless the database answered, and
        // then only briefly, so a wrong answer corrects itself within minutes.
        if ( (int)$ruleId === 0 )
        {
            if ( !self::databaseAnswered() )
            {
                eZDebug::writeWarning( "Not caching a non-match for '$path': the database did not answer", 'expLayoutsResolver' );
                return;
            }
            $ttl = min( $ttl, 300 );
        }

        $key = self::cacheKey( $path, $siteAccessName );
        $data = array(
            'path' => $path,
            'siteaccess' => $siteAccessName,
            'rule_id' => $ruleId,
            'layout_id' => $layoutId,
            'expires' => time() + $ttl,
        );

        self::$resolvedCache[$key] = $data;
        eZFile::create( $key . '.php', self::cacheDir(), '<?php return ' . var_export( $data, true ) . ';', true );
    }

    public static function clearCache()
    {
        self::$resolvedCache = array();
        self::$adminMemo = array();
        self::bumpAdminGeneration();

        $dir = self::cacheDir();
        if ( !is_dir( $dir ) )
            return;

        $files = glob( $dir . '/*.php' );
        if ( $files !== false )
        {
            foreach ( $files as $file )
            {
                @unlink( $file );
            }
        }
    }

    // ------------------------------------------------------------------
    // Admin layouts: resolved by module and view, kept apart from the site.
    // ------------------------------------------------------------------

    private static $adminMemo = array();

    /**
     * The safety switch: [AdminLayoutSettings] Enabled=enabled|disabled.
     * Disabled returns every admin page to plain admin4.
     */
    static function adminLayoutsEnabled()
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        if ( !$ini->hasVariable( 'AdminLayoutSettings', 'Enabled' ) )
            return false;
        return $ini->variable( 'AdminLayoutSettings', 'Enabled' ) === 'enabled';
    }

    /**
     * Whether the current siteaccess is one the admin layouts apply to
     * ([AdminLayoutSettings] SiteAccessMatch, fnmatch patterns).
     */
    static function isAdminSiteAccess( $name = false )
    {
        if ( $name === false )
        {
            $current = eZSiteAccess::current();
            $name = ( is_array( $current ) && isset( $current['name'] ) ) ? $current['name'] : '';
        }
        $ini = eZINI::instance( 'explayouts.ini' );
        if ( $name === '' || !$ini->hasVariable( 'AdminLayoutSettings', 'SiteAccessMatch' ) )
            return false;
        foreach ( (array)$ini->variable( 'AdminLayoutSettings', 'SiteAccessMatch' ) as $pattern )
        {
            if ( $pattern !== '' && fnmatch( $pattern, $name ) )
                return true;
        }
        return false;
    }

    /**
     * The module and view of the request being served, as the kernel records
     * them (eZRequestedModuleParams), or false.
     */
    static function requestedModuleView()
    {
        if ( empty( $GLOBALS['eZRequestedModuleParams']['module_name'] ) )
            return false;
        $params = $GLOBALS['eZRequestedModuleParams'];
        return array( (string)$params['module_name'], isset( $params['function_name'] ) ? (string)$params['function_name'] : '' );
    }

    private static function adminGenerationFile()
    {
        $varDir = eZINI::instance( 'site.ini' )->variable( 'FileSettings', 'VarDir' );
        return $varDir . '/cache/explayouts/admin_generation';
    }

    /**
     * Token that changes whenever an admin layout is published or a rule
     * changes. It is part of every admin cache key, so every cached answer
     * and rendered zone made before the change stops being found.
     */
    static function adminGeneration()
    {
        $file = self::adminGenerationFile();
        $token = is_file( $file ) ? trim( (string)@file_get_contents( $file ) ) : '';
        if ( $token === '' )
            $token = self::bumpAdminGeneration();
        return $token;
    }

    private static function bumpAdminGeneration()
    {
        $token = dechex( time() ) . bin2hex( random_bytes( 3 ) );
        eZFile::create( 'admin_generation', dirname( self::adminGenerationFile() ), $token, true );
        return $token;
    }

    /**
     * The same hash the admin pagelayout uses for "this user's permissions".
     */
    static function adminUserHash()
    {
        $user = eZUser::currentUser();
        return implode( ',', $user->roleIDList() ) . ',' . implode( ',', $user->limitValueList() );
    }

    /**
     * Values for a cache-block keys= list: everything the resolved admin
     * layout depends on. Siteaccess, module, view, user permissions and the
     * generation (changes on publish / rule change).
     */
    static function adminCacheKey( $module = false, $view = false )
    {
        if ( $module === false || $module === '' )
        {
            $mv = self::requestedModuleView();
            $module = $mv ? $mv[0] : '';
            $view = $mv ? $mv[1] : '';
        }
        $current = eZSiteAccess::current();
        return array(
            self::adminGeneration(),
            ( is_array( $current ) && isset( $current['name'] ) ) ? $current['name'] : '',
            (string)$module,
            (string)$view,
            self::adminUserHash(),
        );
    }

    /**
     * The published admin layout for a module and view, or false (plain admin4).
     *
     * False when the admin layouts are disabled, when the siteaccess is not an
     * admin one, or when nothing resolves. Only rules whose layout is of an
     * admin type are looked at; the first match by priority wins, then the
     * DefaultLayout setting.
     */
    static function resolveAdmin( $module = false, $view = false )
    {
        if ( !self::adminLayoutsEnabled() || !self::isAdminSiteAccess() )
            return false;

        if ( $module === false || $module === '' )
        {
            $mv = self::requestedModuleView();
            if ( !$mv )
                return false;
            $module = $mv[0];
            $view = $mv[1];
        }
        $module = (string)$module;
        $view = (string)$view;

        $current = eZSiteAccess::current();
        $siteAccessName = ( is_array( $current ) && isset( $current['name'] ) ) ? $current['name'] : 'default';
        $generation = self::adminGeneration();
        $memoKey = $generation . '|' . $siteAccessName . '|' . $module . '|' . $view;
        if ( array_key_exists( $memoKey, self::$adminMemo ) )
            return self::$adminMemo[$memoKey];

        $cached = self::readCache( 'admin|' . $module . '|' . $view, $siteAccessName );
        if ( $cached !== false && isset( $cached['generation'] ) && $cached['generation'] === $generation )
        {
            $layout = (int)$cached['layout_id'] > 0 ? expLayoutsLayout::fetch( $cached['layout_id'] ) : false;
            if ( $layout && (int)$layout->attribute( 'status' ) === 2 )
                return self::$adminMemo[$memoKey] = $layout;
            if ( (int)$cached['layout_id'] === 0 )
                return self::$adminMemo[$memoKey] = false;
        }

        $context = array( 'module' => $module, 'view' => $view );
        $path = $module . '/' . $view;
        $found = false;
        $ruleId = 0;
        foreach ( expLayoutsRule::fetchEnabled() as $rule )
        {
            $layout = expLayoutsLayout::fetch( $rule->attribute( 'layout_id' ) );
            if ( !$layout || (int)$layout->attribute( 'status' ) !== 2
                || !expLayoutsLayoutType::isAdminType( $layout->attribute( 'layout_type' ) ) )
                continue;
            if ( self::ruleMatches( $rule, $path, $context ) )
            {
                $found = $layout;
                $ruleId = (int)$rule->attribute( 'id' );
                break;
            }
        }

        if ( !$found )
        {
            $default = eZINI::instance( 'explayouts.ini' )->variable( 'AdminLayoutSettings', 'DefaultLayout' );
            if ( $default )
            {
                $layout = expLayoutsLayout::fetchByIdentifier( $default, 2 );
                if ( $layout && expLayoutsLayoutType::isAdminType( $layout->attribute( 'layout_type' ) ) )
                    $found = $layout;
            }
        }

        self::writeAdminCache( $module, $view, $siteAccessName, $generation, $ruleId, $found ? (int)$found->attribute( 'id' ) : 0 );
        return self::$adminMemo[$memoKey] = $found;
    }

    private static function writeAdminCache( $module, $view, $siteAccessName, $generation, $ruleId, $layoutId )
    {
        // As for the site: a non-match is only remembered when the database answered.
        if ( $layoutId === 0 && !self::databaseAnswered() )
            return;

        $ttl = (int)eZINI::instance( 'explayouts.ini' )->variable( 'AdminLayoutSettings', 'CacheTTL' );
        if ( $ttl <= 0 )
            $ttl = 3600;
        if ( $layoutId === 0 )
            $ttl = min( $ttl, 300 );

        $path = 'admin|' . $module . '|' . $view;
        $key = self::cacheKey( $path, $siteAccessName );
        $data = array(
            'path' => $path,
            'siteaccess' => $siteAccessName,
            'rule_id' => $ruleId,
            'layout_id' => $layoutId,
            'generation' => $generation,
            'expires' => time() + $ttl,
        );
        self::$resolvedCache[$key] = $data;
        eZFile::create( $key . '.php', self::cacheDir(), '<?php return ' . var_export( $data, true ) . ';', true );
    }

    static function ruleMatches( $rule, $path, $context = false )
    {
        $targets = $rule->targets();
        $conditions = $rule->conditions();

        foreach ( $conditions as $condition )
        {
            if ( !self::conditionMatches( $condition, $path ) )
                return false;
        }

        if ( count( $targets ) === 0 )
            return true;

        foreach ( $targets as $target )
        {
            if ( self::targetMatches( $target, $path, $context ) )
                return true;
        }
        return false;
    }

    /**
     * Whether a module/view pattern matches. "*" is everything, "content/*"
     * every view of a module, "content/view" one view, "content" a module.
     */
    static function moduleViewMatches( $pattern, $module, $view )
    {
        $pattern = trim( (string)$pattern );
        if ( $pattern === '*' )
            return true;
        $parts = explode( '/', $pattern, 2 );
        if ( $parts[0] !== '*' && $parts[0] !== $module )
            return false;
        if ( !isset( $parts[1] ) || $parts[1] === '*' )
            return true;
        return $parts[1] === $view;
    }

    static function targetMatches( $target, $path, $context = false )
    {
        $type = $target->attribute( 'target_type' );
        $value = $target->attribute( 'target_value' );

        // Module/view targets belong to the admin layouts and need a request
        // context; the public site never supplies one, so they never match there.
        if ( $type === 'module' || $type === 'module_view' )
        {
            if ( !is_array( $context ) )
                return false;
            return self::moduleViewMatches( $value, $context['module'], $context['view'] );
        }
        if ( is_array( $context ) )
            return false;

        switch ( $type )
        {
            case 'path_prefix':
                return strpos( $path, $value ) === 0;
            case 'path_info_prefix':
                return strpos( '/' . ltrim( $path, '/' ), $value ) === 0;
            case 'path':
                return $path === $value;
            case 'path_regex':
                return (bool) preg_match( '/' . str_replace( '/', '\\/', $value ) . '/', $path );
            case 'node':
                return self::contentNodeMatches( $path, $value );
            case 'subtree':
                return self::subtreeMatches( $path, $value );
            case 'route':
            default:
                return false;
        }
    }

    /**
     * Resolve the request path to its content node (url alias, /view/full/N,
     * or the site index page for the root path).
     */
    static function nodeFromPath( $path )
    {
        $path = ltrim( (string)$path, '/' );

        // Drop a leading siteaccess segment.
        //
        // MatchOrder here is "uri;host", so the same page is reachable as
        // /running with a matching host and as /site/running without one. In
        // the second form the request URI still carries the siteaccess name,
        // and a path of "site/running" translates to nothing -- so anything
        // resolving the current node this way got false, and every dynamic
        // collection that asks "what is the current page about" returned an
        // empty list. The page rendered, with its lists silently empty.
        //
        // Only a segment that names a declared siteaccess is removed, so a
        // real content path that happens to start with a similar word is
        // left alone.
        if ( $path !== '' )
        {
            $first = strtok( $path, '/' );
            $siteAccessList = eZINI::instance( 'site.ini' )
                ->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );

            if ( is_array( $siteAccessList ) && in_array( $first, $siteAccessList, true ) )
            {
                $rest = substr( $path, strlen( $first ) );
                $path = ltrim( (string)$rest, '/' );
            }
        }

        if ( $path === '' || $path === 'home' )
        {
            $homePage = eZINI::instance( 'site.ini' )->variable( 'SiteSettings', 'IndexPage' );
            $homeNodeId = 2;
            if ( preg_match( '#/view/full/(\d+)#', $homePage, $m ) )
                $homeNodeId = (int)$m[1];
            return eZContentObjectTreeNode::fetch( $homeNodeId );
        }

        if ( preg_match( '#/view/full/(\d+)#', $path, $m ) )
            return eZContentObjectTreeNode::fetch( (int)$m[1] );

        // translate() resolves multi-level aliases case-insensitively;
        // fetchByPath() only matches single stored rows and misses most paths.
        $uri = $path;
        eZURLAliasML::translate( $uri );
        if ( preg_match( '#content/view/full/(\d+)#', $uri, $m ) )
            return eZContentObjectTreeNode::fetch( (int)$m[1] );

        $alias = eZURLAliasML::fetchByPath( $path, false );
        if ( $alias && is_object( $alias ) )
        {
            $action = $alias->attribute( 'action' );
            if ( preg_match( '#^eznode:(\d+)$#', $action, $m ) )
                return eZContentObjectTreeNode::fetch( (int)$m[1] );
        }
        return false;
    }

    static function subtreeMatches( $path, $value )
    {
        $node = self::nodeFromPath( $path );
        if ( !$node )
            return false;
        $pathArray = $node->attribute( 'path_array' );
        if ( !is_array( $pathArray ) )
            return false;
        return in_array( (int)$value, array_map( 'intval', $pathArray ) );
    }

    static function contentNodeMatches( $path, $value )
    {
        if ( $path === null )
            $path = '';

        $path = ltrim( $path, '/' );

        if ( is_numeric( $value ) )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$value );
            // If fetch by node_id fails, try by object_id (handles data sync offsets)
            if ( !$node )
            {
                $object = eZContentObject::fetch( (int)$value );
                if ( $object )
                {
                    $nodeList = $object->attribute( 'assigned_nodes' );
                    $node = !empty( $nodeList ) ? $nodeList[0] : false;
                }
                if ( !$node )
                    return false;
            }
            if ( !$node )
                return false;
            if ( $path === '' || $path === 'home' )
            {
                $homePage = eZINI::instance( 'site.ini' )->variable( 'SiteSettings', 'IndexPage' );
                $homeNodeId = 2;
                if ( preg_match( '#/view/full/(\d+)#', $homePage, $m ) )
                    $homeNodeId = (int)$m[1];
                return (int)$value === $homeNodeId;
            }
            if ( preg_match( '#/view/full/' . (int)$value . '($|/)#', $path ) )
                return true;

            // Resolve the request path to its node and compare ids; also treat
            // the target as matched when it stores the node's object id.
            $pathNode = self::nodeFromPath( $path );
            if ( $pathNode )
            {
                if ( (int)$pathNode->attribute( 'node_id' ) === (int)$value )
                    return true;
                if ( (int)$pathNode->attribute( 'contentobject_id' ) === (int)$value && !eZContentObjectTreeNode::fetch( (int)$value ) )
                    return true;
            }
            return false;
        }

        $node = eZContentObjectTreeNode::fetchByURLPath( $path, false );
        if ( !$node )
            return false;
        return isset( $node['url_alias'] ) && $node['url_alias'] === $value;
    }

    static function conditionMatches( $condition, $path = '' )
    {
        $type = $condition->attribute( 'condition_type' );
        $value = $condition->attribute( 'condition_value' );

        switch ( $type )
        {
            case 'siteaccess':
            {
                $siteAccesses = json_decode( $value, true );
                if ( !is_array( $siteAccesses ) )
                    $siteAccesses = array( (string)$value );

                $current = eZSiteAccess::current();
                if ( !is_array( $current ) || !isset( $current['name'] ) )
                    return false;

                return in_array( $current['name'], $siteAccesses );
            }
            case 'content_type':
            case 'class':
            {
                $classes = json_decode( $value, true );
                if ( !is_array( $classes ) )
                    $classes = array( (string)$value );
                $node = self::nodeFromPath( $path );
                if ( !$node )
                    return false;
                return in_array( $node->attribute( 'class_identifier' ), $classes );
            }
            case 'query_parameter':
            {
                $params = json_decode( $value, true );
                if ( !is_array( $params ) )
                    $params = array( 'parameter_name' => (string)$value );

                $name = isset( $params['parameter_name'] ) ? trim( $params['parameter_name'] ) : '';
                if ( $name === '' )
                    return false;

                if ( !isset( $_GET[$name] ) )
                    return false;

                $paramValue = trim( (string)$_GET[$name] );
                $paramValues = isset( $params['parameter_values'] ) ? $params['parameter_values'] : array();
                if ( !is_array( $paramValues ) )
                    $paramValues = array( (string)$paramValues );

                if ( count( $paramValues ) === 0 )
                    return true;

                foreach ( $paramValues as $v )
                {
                    if ( trim( (string)$v ) === $paramValue )
                        return true;
                }

                return false;
            }
            case 'route_parameter':
            {
                $params = json_decode( $value, true );
                if ( !is_array( $params ) )
                    $params = array( 'parameter_name' => (string)$value );

                $name = isset( $params['parameter_name'] ) ? trim( $params['parameter_name'] ) : '';
                if ( $name === '' )
                    return false;

                $uri = eZURI::instance( $path );
                $routeParams = $uri ? $uri->userParameters() : array();
                if ( !isset( $routeParams[$name] ) )
                    return false;

                $paramValue = trim( (string)$routeParams[$name] );
                $paramValues = isset( $params['parameter_values'] ) ? $params['parameter_values'] : array();
                if ( !is_array( $paramValues ) )
                    $paramValues = array( (string)$paramValues );

                if ( count( $paramValues ) === 0 )
                    return true;

                foreach ( $paramValues as $v )
                {
                    if ( trim( (string)$v ) === $paramValue )
                        return true;
                }

                return false;
            }
            case 'time':
            {
                $range = json_decode( $value, true );
                if ( !is_array( $range ) )
                    return false;

                $from = isset( $range['from'] ) ? $range['from'] : null;
                $to = isset( $range['to'] ) ? $range['to'] : null;

                if ( empty( $from ) && empty( $to ) )
                    return true;

                $fromTs = self::conditionTimeToTimestamp( $from );
                $toTs = self::conditionTimeToTimestamp( $to );
                $now = time();

                if ( $fromTs !== null && $now < $fromTs )
                    return false;
                if ( $toTs !== null && $now > $toTs )
                    return false;

                return true;
            }
            default:
                return true;
        }
    }

    private static function conditionTimeToTimestamp( $value )
    {
        if ( empty( $value ) )
            return null;

        if ( is_array( $value ) )
        {
            $year = isset( $value['year'] ) ? (int)$value['year'] : 0;
            $month = isset( $value['month'] ) ? (int)$value['month'] : 0;
            $day = isset( $value['day'] ) ? (int)$value['day'] : 0;
            $hour = isset( $value['hour'] ) ? (int)$value['hour'] : 0;
            $minute = isset( $value['minute'] ) ? (int)$value['minute'] : 0;
            $second = isset( $value['second'] ) ? (int)$value['second'] : 0;

            if ( $year > 0 && $month > 0 && $day > 0 )
                return mktime( $hour, $minute, $second, $month, $day, $year );

            return null;
        }

        $timestamp = strtotime( (string)$value );
        return $timestamp !== false ? $timestamp : null;
    }
}
