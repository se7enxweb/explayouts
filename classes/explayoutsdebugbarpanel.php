<?php
/**
 * The Exp Layouts panel of the Exp Debug bar: which layout built the page, which rule chose it, its zones and blocks
 * with links to edit them in the administration, and the blocks that are available.
 *
 * Registered in settings/debugbar.ini.append.php ([DebugBarSettings] Panels[]=explayouts); the kernel's debug bar
 * draws it from Exponential 6.0.15 on (expDebugBarPanelInterface). It reads what expLayoutsResolver::resolve() answered
 * for the path of the request (pageResolution(); a page also resolves the layouts of other nodes, those are not its)
 * and what expLayoutsRenderer::prepareLayout() prepared for that layout. Its own queries are the page's node (when the
 * request's path is not what the page resolved) and the layout's name when it was resolved but not rendered.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package explayouts
 */
class expLayoutsDebugBarPanel implements expDebugBarPanelInterface
{
    const CONTEXT = 'extension/explayouts/debugbar';

    /**
     * Only on the public site, only when the layout of this page was looked up, and only for users who may edit
     * layouts: the bar also shows to anonymous visitors when DebugByIP matches, and layout ids and admin links are
     * not for them.
     */
    public function visible()
    {
        if ( expLayoutsResolver::isAdminSiteAccess() )
            return false;
        // the rights first: the page's node is looked up only for those who may see the panel
        $access = eZUser::currentUser()->hasAccessTo( 'explayouts_ui', 'edit' );
        if ( !isset( $access['accessWord'] ) || $access['accessWord'] === 'no' )
            return false;
        return $this->resolution() !== null;
    }

    /** @var array|null|false the page's resolution, false while not looked up */
    private $resolution = false;

    /** What the resolver answered for this page ($this->resolution()), looked up once. */
    protected function resolution()
    {
        if ( $this->resolution === false )
            $this->resolution = expLayoutsResolver::pageResolution( self::pageNodeID() );
        return $this->resolution;
    }

    /** The node the page shows, from the module result as the debug bar reads it, or 0. */
    protected static function pageNodeID()
    {
        try
        {
            $tpl = eZTemplate::factory();
            if ( $tpl->hasVariable( 'module_result' ) )
            {
                $result = $tpl->variable( 'module_result' );
                if ( isset( $result['content_info']['node_id'] ) )
                    return (int)$result['content_info']['node_id'];
            }
        }
        catch ( Throwable $e )
        {
        }
        return 0;
    }

    public function title()
    {
        return self::t( 'Exp Layouts' );
    }

    public function chip()
    {
        $layout = $this->layout();
        $zones = $this->zones();
        $blocks = 0;
        foreach ( $zones as $zone )
            $blocks += count( $zone['blocks'] );
        return array(
            'text' => self::t( 'Layout: %name', array( '%name' => $layout ? $layout['name'] : self::t( 'none' ) ) ),
            'title' => $this->resolvedBy() . ( $layout ? ' - ' . self::t( '%zones zones, %blocks blocks', array( '%zones' => count( $zones ), '%blocks' => $blocks ) ) : '' ),
            'level' => 'ok',
        );
    }

    public function html()
    {
        $layout = $this->layout();
        $resolution = $this->resolution();
        $html = '<table class="exp-debug-facts expl-debug-layout">';
        $html .= self::row( self::t( 'Layout' ), $layout ? $layout['name'] : self::t( 'none' ) );
        if ( $layout )
        {
            $html .= self::row( self::t( 'Identifier' ), $layout['identifier'] );
            $html .= self::row( 'ID', $layout['id'] );
            $html .= self::row( self::t( 'Type' ), $layout['layout_type'] );
        }
        $html .= self::row( self::t( 'Chosen by' ), $this->resolvedBy() );
        if ( $resolution )
        {
            $html .= self::row( self::t( 'Path' ), '/' . $resolution['path'] );
            $html .= self::row( self::t( 'Siteaccess' ), $resolution['siteaccess'] );
        }
        $html .= '</table>';

        $links = array();
        if ( $layout )
            $links[] = self::link( self::adminURL( 'explayouts_ui/layout_edit/' . (int)$layout['id'] ), self::t( 'Edit layout' ) );
        if ( $resolution && $resolution['rule_id'] > 0 )
            $links[] = self::link( self::adminURL( 'explayouts_ui/rule_edit/' . (int)$resolution['rule_id'] ), self::t( 'Edit rule' ) );
        $links[] = self::link( self::adminURL( 'explayouts_ui/rule_list' ), self::t( 'All rules' ) );
        $links[] = self::link( self::currentURLWith( 'expl_debug', '1' ), self::t( 'Show the blocks on the page' ) );
        $html .= '<p class="expl-debug-links">' . implode( ' · ', $links ) . '</p>';

        $html .= $this->zonesHtml();
        $html .= $this->availableBlocksHtml();
        return $html;
    }

    public function data()
    {
        $layout = $this->layout();
        $resolution = $this->resolution();
        $blocks = 0;
        foreach ( $this->zones() as $zone )
            $blocks += count( $zone['blocks'] );
        return array(
            'layout_id' => $layout ? (int)$layout['id'] : 0,
            'identifier' => $layout ? (string)$layout['identifier'] : null,
            'rule_id' => $resolution ? (int)$resolution['rule_id'] : 0,
            'source' => $resolution ? $resolution['source'] : null,
            'zones' => count( $this->zones() ),
            'blocks' => $blocks,
        );
    }

    /**
     * The layout the resolver chose for the page: as it was prepared for rendering, else its name read once.
     *
     * @return array|null id, identifier, name, layout_type (and zones when it was prepared)
     */
    protected function layout()
    {
        $resolution = $this->resolution();
        if ( !$resolution || $resolution['layout_id'] <= 0 )
            return null;
        $prepared = expLayoutsRenderer::prepared( $resolution['layout_id'] );
        if ( is_array( $prepared ) )
            return $prepared;
        $layout = expLayoutsLayout::fetch( $resolution['layout_id'] );
        if ( !$layout instanceof expLayoutsLayout )
            return null;
        return array( 'id' => $layout->attribute( 'id' ), 'identifier' => $layout->attribute( 'identifier' ),
                      'name' => $layout->attribute( 'name' ), 'layout_type' => $layout->attribute( 'layout_type' ) );
    }

    /** @return array the zones of the page's layout as it was prepared, or none when it was not rendered */
    protected function zones()
    {
        $layout = $this->layout();
        return is_array( $layout ) && isset( $layout['zones'] ) ? (array)$layout['zones'] : array();
    }

    /** How the layout was chosen, in words. */
    protected function resolvedBy()
    {
        $r = $this->resolution();
        if ( !$r )
            return self::t( 'Not looked up by the resolver on this page' );
        switch ( $r['source'] )
        {
            case 'rule':
                return self::t( 'Rule %id', array( '%id' => $r['rule_id'] ) );
            case 'cache':
                if ( $r['layout_id'] <= 0 )
                    return self::t( 'No rule (from the resolver cache)' );
                return $r['rule_id'] > 0
                    ? self::t( 'Rule %id (from the resolver cache)', array( '%id' => $r['rule_id'] ) )
                    : self::t( 'No rule matched: the default layout (from the resolver cache)' );
            case 'default':
                return self::t( 'No rule matched: the default layout (explayouts.ini DefaultLayout)' );
        }
        return self::t( 'No rule matched and no default layout' );
    }

    protected function zonesHtml()
    {
        $zones = $this->zones();
        if ( !$zones )
            return '<p class="exp-debug-hint">' . self::h( self::t( 'The layout was not rendered on this page: no zones to show.' ) ) . '</p>';
        $names = self::blockNames();
        // block_edit asks for explayouts/edit itself: the link only for those who have it
        $access = eZUser::currentUser()->hasAccessTo( 'explayouts', 'edit' );
        $mayEditBlocks = isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
        $html = '<h4>' . self::h( self::t( 'Zones and blocks' ) ) . '</h4>';
        $html .= '<table class="exp-debug-facts expl-debug-zones"><tr><th>' . self::h( self::t( 'Zone' ) ) . '</th><th>' . self::h( self::t( 'Block' ) )
               . '</th><th>' . self::h( self::t( 'Kind' ) ) . '</th><th>' . self::h( self::t( 'View' ) ) . '</th><th>ID</th><th></th></tr>';
        foreach ( $zones as $zone )
        {
            $label = (string)$zone['identifier'];
            if ( !empty( $zone['linked_layout_id'] ) )
                $label .= ' ' . self::t( '(linked: layout %layout, zone %zone)', array( '%layout' => $zone['linked_layout_id'], '%zone' => $zone['linked_zone_identifier'] ) );
            if ( !$zone['blocks'] )
            {
                $html .= '<tr><td>' . self::h( $label ) . '</td><td colspan="5">' . self::h( self::t( 'empty' ) ) . '</td></tr>';
                continue;
            }
            foreach ( self::flatten( $zone['blocks'] ) as $i => $item )
            {
                list( $block, $depth ) = $item;
                $kind = (string)$block['definition_identifier'];
                $html .= '<tr><td>' . ( $i === 0 ? self::h( $label ) : '' ) . '</td>'
                       . '<td>' . str_repeat( '&#8627; ', $depth ) . self::h( (string)$block['name'] !== '' ? $block['name'] : '-' ) . '</td>'
                       . '<td>' . self::h( isset( $names[$kind] ) ? $names[$kind] . ' (' . $kind . ')' : $kind ) . '</td>'
                       . '<td>' . self::h( $block['view_type'] ) . '</td>'
                       . '<td>' . (int)$block['id'] . '</td>'
                       . '<td>' . ( $mayEditBlocks ? self::link( self::adminURL( 'explayouts_ui/block_edit/' . (int)$block['id'] ), self::t( 'Edit' ) ) : '' ) . '</td></tr>';
            }
        }
        return $html . '</table>';
    }

    /**
     * The blocks of a zone with their nesting depth, top level blocks in order with their children after them. A block
     * whose parent is not in the zone counts as a top level block, as the renderer draws it.
     *
     * @return array list of array( block, depth )
     */
    protected static function flatten( array $blocks )
    {
        $out = array();
        $walk = function ( array $list, $depth ) use ( &$walk, &$out )
        {
            foreach ( $list as $block )
            {
                $out[] = array( $block, $depth );
                if ( !empty( $block['children'] ) )
                    $walk( $block['children'], $depth + 1 );
            }
        };
        $ids = array();
        foreach ( $blocks as $block )
            $ids[(int)$block['id']] = true;
        $top = array();
        foreach ( $blocks as $block )
        {
            if ( empty( $block['parent_id'] ) || !isset( $ids[(int)$block['parent_id']] ) )
                $top[] = $block;
        }
        $walk( $top, 0 );
        return $out;
    }

    protected function availableBlocksHtml()
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        $available = $ini->hasVariable( 'BlockSettings', 'AvailableBlocks' )
            ? (array)$ini->variable( 'BlockSettings', 'AvailableBlocks' ) : array_keys( self::blockNames() );
        $html = '<details class="expl-debug-available"><summary>' . self::h( self::t( 'Available blocks (%count)', array( '%count' => count( $available ) ) ) ) . '</summary>';
        $html .= '<table class="exp-debug-facts"><tr><th>' . self::h( self::t( 'Kind' ) ) . '</th><th>' . self::h( self::t( 'Name' ) ) . '</th><th>'
               . self::h( self::t( 'Category' ) ) . '</th><th>' . self::h( self::t( 'Views' ) ) . '</th></tr>';
        foreach ( $available as $identifier )
        {
            $group = 'BlockDefinition_' . $identifier;
            $value = function ( $name, $default = '' ) use ( $ini, $group )
            {
                return $ini->hasVariable( $group, $name ) ? $ini->variable( $group, $name ) : $default;
            };
            $html .= '<tr><td>' . self::h( $identifier ) . '</td><td>' . self::h( $value( 'Name', $identifier ) ) . '</td><td>'
                   . self::h( $value( 'Category' ) ) . '</td><td>' . self::h( implode( ', ', (array)$value( 'ViewTypes', array() ) ) ) . '</td></tr>';
        }
        return $html . '</table></details>';
    }

    /** @return array identifier => Name of every [BlockDefinition_*] group */
    protected static function blockNames()
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        $names = array();
        foreach ( array_keys( $ini->groups() ) as $group )
        {
            if ( strpos( $group, 'BlockDefinition_' ) === 0 )
            {
                $identifier = substr( $group, strlen( 'BlockDefinition_' ) );
                $names[$identifier] = $ini->hasVariable( $group, 'Name' ) ? (string)$ini->variable( $group, 'Name' ) : $identifier;
            }
        }
        return $names;
    }

    /**
     * An address in the administration: explayouts.ini [DebugBarSettings] AdminURL (e.g. https://admin.example.com)
     * when set, else the first plain name of [AdminLayoutSettings] SiteAccessMatch[] after the site's directory, which
     * fits siteaccesses matched by URI.
     */
    public static function adminURL( $path )
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        $base = $ini->hasVariable( 'DebugBarSettings', 'AdminURL' ) ? rtrim( (string)$ini->variable( 'DebugBarSettings', 'AdminURL' ), '/' ) : '';
        if ( $base === '' )
        {
            $siteAccess = 'admin';
            if ( $ini->hasVariable( 'AdminLayoutSettings', 'SiteAccessMatch' ) )
            {
                foreach ( (array)$ini->variable( 'AdminLayoutSettings', 'SiteAccessMatch' ) as $pattern )
                {
                    if ( preg_match( '/^[A-Za-z0-9_]+$/', (string)$pattern ) )
                    {
                        $siteAccess = $pattern;
                        break;
                    }
                }
            }
            $base = rtrim( eZSys::wwwDir(), '/' ) . '/' . $siteAccess;
        }
        return $base . '/' . ltrim( $path, '/' );
    }

    /** The address of this page with $name=$value added to its query. */
    protected static function currentURLWith( $name, $value )
    {
        $uri = isset( $_SERVER['REQUEST_URI'] ) ? (string)$_SERVER['REQUEST_URI'] : '/';
        $parts = explode( '?', $uri, 2 );
        parse_str( isset( $parts[1] ) ? $parts[1] : '', $query );
        $query[$name] = $value;
        return $parts[0] . '?' . http_build_query( $query );
    }

    protected static function row( $label, $value )
    {
        return '<tr><th>' . self::h( $label ) . '</th><td>' . self::h( $value ) . '</td></tr>';
    }

    protected static function link( $url, $text )
    {
        return '<a href="' . self::h( $url ) . '">' . self::h( $text ) . '</a>';
    }

    protected static function h( $text )
    {
        return htmlspecialchars( (string)$text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
    }

    protected static function t( $source, array $arguments = array() )
    {
        return class_exists( 'ezpI18n' ) ? ezpI18n::tr( self::CONTEXT, $source, null, $arguments ) : strtr( $source, $arguments );
    }
}
