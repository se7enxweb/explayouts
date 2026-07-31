<?php


class expLayoutsSiteBundleEntity
{
    public static function find( $className, $id )
    {
        if ( class_exists( $className ) && method_exists( $className, 'fetch' ) )
            return call_user_func( array( $className, 'fetch' ), (int)$id );

        return false;
    }


}
