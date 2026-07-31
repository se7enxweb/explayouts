<?php


class expLayoutsExpAdminUI
{
    public function getRelatedLayouts( $contentId )
    {
        return array();
    }


    public function getLayoutsForContent( $contentId )
    {
        $contentId = (int)$contentId;
        if ( $contentId <= 0 )
            return array();
        $layouts = expLayoutsLayout::fetchList();
        $result = array();
        foreach ( $layouts as $layout )
        {
            if ( $layout instanceof expLayoutsLayout && $layout->attribute( 'status' ) === 'published' )
                $result[] = $layout;
        }
        return $result;
    }
}
