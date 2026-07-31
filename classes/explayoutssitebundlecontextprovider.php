<?php


class expLayoutsSiteBundleContextProvider
{
    public static function getContext( $locationId )
    {
        $location = expLayoutsSiteAPI::loadLocation( (int)$locationId );
        return array(
            'location' => $location,
            'content' => $location instanceof eZContentObjectTreeNode ? $location->object() : false,
            'path' => $location instanceof eZContentObjectTreeNode ? $location->pathArray() : array(),
        );
    }
}
