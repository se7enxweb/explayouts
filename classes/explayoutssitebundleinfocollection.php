<?php


class expLayoutsSiteBundleInfoCollection
{
    public static function collect( $contentId, array $data = array() )
    {
        if ( !class_exists( 'eZInformationCollection' ) )
            return false;

        $collection = eZInformationCollection::create(
            (int)$contentId,
            eZUser::currentUser(),
            eZUser::currentUser(),
            1,
            eZSys::clientIP(),
            $data
        );

        if ( $collection instanceof eZInformationCollection )
            return $collection->store();

        return false;
    }


    public static function submit( $contentId, array $data = array() )
    {
        $db = eZDB::instance();
        $db->query( 'INSERT INTO sevenx_info_collection (contentobject_id, data, created) VALUES (' . (int)$contentId . ', \'' . $db->escapeString( json_encode( $data ) ) . '\', ' . time() . ')' );
        return true;
    }
}
