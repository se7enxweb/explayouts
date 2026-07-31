<?php


class expLayoutsSiteBundleCore
{
    public static function getConfig( $name, $default = null )
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        if ( $ini->hasVariable( 'Core', $name ) )
            return $ini->variable( 'Core', $name );

        return $default;
    }

    public static function getRootLocationId()
    {
        return (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' );
    }


    public static function getSetting( $name, $default = null, $group = 'SiteSettings' )
    {
        $ini = eZINI::instance( 'site.ini' );
        return $ini->hasVariable( $group, $name ) ? $ini->variable( $group, $name ) : $default;
    }
}
