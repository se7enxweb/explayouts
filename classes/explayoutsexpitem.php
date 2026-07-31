<?php


class expLayoutsExpItem
{
    public function convertContent( $content )
    {
        return expLayoutsExpSiteApi::convertContentToItem( $content );
    }

    public function convertLocation( $location )
    {
        return expLayoutsExpSiteApi::convertLocationToItem( $location );
    }

    public function getUrl( $item )
    {
        if ( is_array( $item ) && isset( $item['url'] ) )
            return $item['url'];
        return '';
    }


    public function getContentId( $item )
    {
        if ( is_array( $item ) && isset( $item['content_id'] ) )
            return (int)$item['content_id'];
        if ( is_array( $item ) && isset( $item['id'] ) )
            return (int)$item['id'];
        return 0;
    }

    public function getLocationId( $item )
    {
        return is_array( $item ) && isset( $item['location'] ) && $item['location'] instanceof eZContentObjectTreeNode ? (int)$item['location']->attribute( 'node_id' ) : 0;
    }
}
