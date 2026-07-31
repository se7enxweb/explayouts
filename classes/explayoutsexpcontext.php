<?php


class expLayoutsExpContext
{
    public function getContext( $locationId = null )
    {
        return expLayoutsSiteBundleContextProvider::getContext( $locationId );
    }


    public function getLayoutContext( $layoutId )
    {
        $layout = expLayoutsLayout::fetch( (int)$layoutId );
        if ( !$layout instanceof expLayoutsLayout )
            return array();
        return array(
            'layout' => $layout,
            'zones' => $layout->attribute( 'zones' ),
        );
    }
}
