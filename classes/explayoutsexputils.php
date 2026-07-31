<?php


class expLayoutsExpUtils
{
    public static function trim( $string )
    {
        return trim( (string)$string );
    }


    public static function slugify( $string )
    {
        $string = strtolower( trim( $string ) );
        $string = preg_replace( '/[^a-z0-9]+/', '-', $string );
        return trim( $string, '-' );
    }

    public static function truncate( $string, $length = 100 )
    {
        return strlen( $string ) > $length ? substr( $string, 0, $length ) . '...' : $string;
    }
}
