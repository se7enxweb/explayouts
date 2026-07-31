<?php


class expLayoutsExpSecurity
{
    public function hasAccess( $module, $function )
    {
        return expLayoutsSiteBundleSecurity::hasAccess( $module, $function );
    }


    public function isLoggedIn()
    {
        $user = eZUser::currentUser();
        return $user instanceof eZUser && (int)$user->attribute( 'contentobject_id' ) > 0;
    }

    public function currentUserId()
    {
        $user = eZUser::currentUser();
        return $user instanceof eZUser ? (int)$user->attribute( 'contentobject_id' ) : 0;
    }
}
