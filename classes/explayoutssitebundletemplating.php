<?php


class expLayoutsSiteBundleTemplating
{
    public static function render( $template, $variables = array() )
    {
        $tpl = eZTemplate::factory();
        foreach ( $variables as $key => $value )
            $tpl->setVariable( $key, $value );

        return $tpl->fetch( $template );
    }


}
