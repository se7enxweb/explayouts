<?php
class expLayoutsResolver
{
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
        if ( $path === '' )
            $path = 'home';

        $rules = expLayoutsRule::fetchEnabled();
        foreach ( $rules as $rule )
        {
            if ( self::ruleMatches( $rule, $path ) )
            {
                $layout = expLayoutsLayout::fetch( $rule->attribute( 'layout_id' ) );
                if ( $layout && (int)$layout->attribute( 'status' ) > 0 )
                {
                    error_log( "expLayoutsResolver: Matched rule " . $rule->attribute( 'id' ) . " for path '$path', layout=" . $layout->attribute( 'identifier' ) );
                    return $layout;
                }
            }
        }
        error_log( "expLayoutsResolver: No rule matched for path '$path'" );

        $ini = eZINI::instance( 'explayouts.ini' );
        $default = $ini->variable( 'ResolverSettings', 'DefaultLayout' );
        if ( $default )
            return expLayoutsLayout::fetchByIdentifier( $default, 2 );

        return false;
    }

    static function ruleMatches( $rule, $path )
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
            if ( self::targetMatches( $target, $path ) )
                return true;
        }
        return false;
    }

    static function targetMatches( $target, $path )
    {
        $type = $target->attribute( 'target_type' );
        $value = $target->attribute( 'target_value' );

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
            case 'content_node':
                return self::contentNodeMatches( $path, $value );
            case 'subtree':
            case 'ibexa_subtree':
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
                $current = eZSiteAccess::current();
                return is_array( $current ) && isset( $current['name'] ) && $current['name'] === $value;
            case 'content_type':
            case 'ibexa_content_type':
            {
                $classes = json_decode( $value, true );
                if ( !is_array( $classes ) )
                    $classes = array( (string)$value );
                $node = self::nodeFromPath( $path );
                if ( !$node )
                    return false;
                return in_array( $node->attribute( 'class_identifier' ), $classes );
            }
            default:
                return true;
        }
    }
}
