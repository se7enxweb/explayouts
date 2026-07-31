<?php


class expLayoutsSiteBundleEvent
{
    protected static $listeners = array();

    public static function addListener( $event, $callback )
    {
        if ( !isset( self::$listeners[$event] ) )
            self::$listeners[$event] = array();

        self::$listeners[$event][] = $callback;
    }

    public static function trigger( $event, $params = array() )
    {
        if ( isset( self::$listeners[$event] ) )
        {
            foreach ( self::$listeners[$event] as $callback )
                call_user_func( $callback, $params );
        }
    }


    public static function dispatch( $eventName, array $args = array() )
    {
        eZDebug::writeNotice( 'Dispatch: ' . $eventName, 'expLayoutsSiteBundleEvent' );
        return $args;
    }
}
