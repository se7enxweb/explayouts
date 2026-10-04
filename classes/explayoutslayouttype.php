<?php
class expLayoutsLayoutType
{
    private static $iconMap = array(
        '1_column' => 'layout_1',
        '2_column' => 'layout_5',
        '3_column' => 'layout_3',
        '4_column' => 'layout_5',
        'hero' => 'layout_6',
        'sidebar_left' => 'layout_3',
        'sidebar_right' => 'layout_4',
        'featured' => 'layout_6',
        'mosaic' => 'layout_6',
        'layout_1' => 'layout_1',
        'layout_2' => 'layout_2',
        'layout_4' => 'layout_4',
    );

    const GROUP_SITE = 'site';
    const GROUP_ADMIN = 'admin';

    /**
     * The layout types, optionally of one group only.
     *
     * $group 'site' (the default) lists the types of the public site, 'admin'
     * the types of the administration interface (LayoutType_*: Group=admin),
     * false every type. The default keeps the admin types out of every place
     * that has always listed "the layout types".
     */
    static function getAvailableTypes( $group = self::GROUP_SITE )
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        $list = array();
        foreach ( $ini->groups() as $groupName => $vars )
        {
            if ( strpos( $groupName, 'LayoutType_' ) === 0 )
            {
                $identifier = substr( $groupName, strlen( 'LayoutType_' ) );
                $typeGroup = isset( $vars['Group'] ) && $vars['Group'] !== '' ? $vars['Group'] : self::GROUP_SITE;
                if ( $group !== false && $typeGroup !== $group )
                    continue;
                $name = isset( $vars['Name'] ) ? $vars['Name'] : $identifier;
                $list[] = array( 'identifier' => $identifier, 'name' => $name, 'group' => $typeGroup );
            }
        }
        return $list;
    }

    /**
     * 'admin' or 'site'. An unknown type is a site type.
     */
    static function getGroup( $identifier )
    {
        static $memo = array();
        $identifier = (string)$identifier;
        if ( !isset( $memo[$identifier] ) )
        {
            $ini = eZINI::instance( 'explayouts.ini' );
            $group = 'LayoutType_' . $identifier;
            $value = $ini->hasGroup( $group ) && $ini->hasVariable( $group, 'Group' ) ? $ini->variable( $group, 'Group' ) : '';
            $memo[$identifier] = $value === self::GROUP_ADMIN ? self::GROUP_ADMIN : self::GROUP_SITE;
        }
        return $memo[$identifier];
    }

    static function isAdminType( $identifier )
    {
        return self::getGroup( $identifier ) === self::GROUP_ADMIN;
    }

    static function getTypeInfo( $identifier )
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        $group = 'LayoutType_' . $identifier;
        if ( !$ini->hasGroup( $group ) )
            return false;

        $name = $ini->variable( $group, 'Name' );
        $zones = $ini->variable( $group, 'Zones' );
        if ( !is_array( $zones ) )
            $zones = array();

        $icon = isset( self::$iconMap[$identifier] ) ? self::$iconMap[$identifier] : 'layout_1';

        return array(
            'identifier' => $identifier,
            'name' => $name,
            'zones' => $zones,
            'icon' => $icon,
            'group' => self::getGroup( $identifier ),
        );
    }

    static function getZones( $identifier )
    {
        $info = self::getTypeInfo( $identifier );
        return $info ? $info['zones'] : array();
    }
}
