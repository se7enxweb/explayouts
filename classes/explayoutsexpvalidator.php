<?php


class expLayoutsExpValidator
{
    public function validateTag( $tag )
    {
        return is_string( $tag ) && $tag !== '';
    }


    public static function validateEmail( $email )
    {
        return filter_var( $email, FILTER_VALIDATE_EMAIL ) !== false;
    }

    public static function validateUrl( $url )
    {
        return filter_var( $url, FILTER_VALIDATE_URL ) !== false;
    }
}
