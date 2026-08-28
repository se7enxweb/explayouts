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

    static function getAvailableTypes()
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        $list = array();
        foreach ( $ini->groups() as $group => $vars )
        {
            if ( strpos( $group, 'LayoutType_' ) === 0 )
            {
                $identifier = substr( $group, strlen( 'LayoutType_' ) );
                $name = isset( $vars['Name'] ) ? $vars['Name'] : $identifier;
                $list[] = array( 'identifier' => $identifier, 'name' => $name );
            }
        }
        return $list;
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
        );
    }

    static function getZones( $identifier )
    {
        $info = self::getTypeInfo( $identifier );
        return $info ? $info['zones'] : array();
    }
}
