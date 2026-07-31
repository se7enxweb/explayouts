<?php


class expLayoutsExpLayout
{
    public function getLayout( $layoutId )
    {
        return expLayoutsSiteBundleLayouts::render( $layoutId );
    }


    public function exists( $layoutId )
    {
        return expLayoutsLayout::fetch( (int)$layoutId ) instanceof expLayoutsLayout;
    }

    public function getLayoutName( $layoutId )
    {
        $layout = expLayoutsLayout::fetch( (int)$layoutId );
        return $layout instanceof expLayoutsLayout ? $layout->attribute( 'name' ) : '';
    }
}
