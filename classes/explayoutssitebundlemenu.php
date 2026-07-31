<?php


class expLayoutsSiteBundleMenu
{
    public static function getChildren( $rootNodeId, $depth = 1, $limit = 25 )
    {
        return expLayoutsSiteAPI::filterChildren(
            (int)$rootNodeId,
            array( 'depth' => (int)$depth, 'limit' => (int)$limit )
        );
    }


    public static function getMenuItems( $menuIdentifier )
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        return $ini->hasGroup( 'Menu_' . $menuIdentifier ) ? $ini->group( 'Menu_' . $menuIdentifier ) : array();
    }
}
