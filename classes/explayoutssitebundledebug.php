<?php


class expLayoutsSiteBundleDebug
{
    public static function dump( $var, $label = '' )
    {
        eZDebug::writeNotice( print_r( $var, true ), $label !== '' ? $label : 'expLayoutsSiteBundleDebug' );
    }


    public static function log( $message, $level = 'debug' )
    {
        eZDebug::writeNotice( $message, 'expLayoutsSiteBundleDebug' );
        return true;
    }
}
