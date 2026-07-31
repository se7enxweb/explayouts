<?php


class expLayoutsSiteBundleLayouts
{
    public static function render( $layoutId )
    {
        return expLayoutsRenderer::renderById( (int)$layoutId );
    }


    public static function getLayoutByRule( $locationId )
    {
        return expLayoutsResolver::resolve( (int)$locationId );
    }
}
