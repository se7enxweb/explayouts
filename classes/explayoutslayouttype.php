<?php
class expLayoutsLayoutType
{
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

        return array(
            'identifier' => $identifier,
            'name' => $name,
            'zones' => $zones,
        );
    }

    static function getZones( $identifier )
    {
        $info = self::getTypeInfo( $identifier );
        return $info ? $info['zones'] : array();
    }
}
