<?php


class expLayoutsSiteBundleController
{
    public static function view( $template, $variables = array() )
    {
        $tpl = eZTemplate::factory();
        foreach ( $variables as $key => $value )
            $tpl->setVariable( $key, $value );

        return $tpl->fetch( $template );
    }


    public static function redirectToLocation( $locationId )
    {
        $node = expLayoutsSiteAPI::loadLocation( (int)$locationId );
        if ( $node instanceof eZContentObjectTreeNode )
        {
            $url = $node->attribute( 'url_alias' );
            eZHTTPTool::redirect( $url );
        }
        return false;
    }
}
