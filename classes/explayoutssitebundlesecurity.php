<?php


class expLayoutsSiteBundleSecurity
{
    public static function hasAccess( $module, $function )
    {
        $user = eZUser::currentUser();
        return $user instanceof eZUser ? $user->hasAccessTo( $module, $function ) : false;
    }


    public static function currentUser()
    {
        return eZUser::currentUser();
    }
}
