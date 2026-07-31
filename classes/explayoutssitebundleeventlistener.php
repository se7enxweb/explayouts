<?php


class expLayoutsSiteBundleEventListener
{
    public static function onContentPublish( $callback )
    {
        expLayoutsSiteBundleEvent::addListener( 'content.publish', $callback );
    }

    public static function onContentUpdate( $callback )
    {
        expLayoutsSiteBundleEvent::addListener( 'content.update', $callback );
    }


    public static function listen( $eventName, $callback )
    {
        return true;
    }
}
