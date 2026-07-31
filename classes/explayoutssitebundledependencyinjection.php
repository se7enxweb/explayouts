<?php


class expLayoutsSiteBundleDependencyInjection
{
    public static function getParameter( $name, $default = null )
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        if ( $ini->hasVariable( 'Settings', $name ) )
            return $ini->variable( 'Settings', $name );

        return $default;
    }

    public static function setParameter( $name, $value )
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        $ini->setVariable( 'Settings', $name, $value );
    }

}
