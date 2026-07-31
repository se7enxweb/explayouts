<?php
class expLayoutsCollection extends eZPersistentObject
{
    static function definition()
    {
        return array(
            "fields" => array(
                "id" => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "block_id" => array( 'name' => 'BlockID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "collection_type" => array( 'name' => 'CollectionType', 'datatype' => 'string', 'default' => 'manual', 'required' => false ),
                "offset_value" => array( 'name' => 'OffsetValue', 'datatype' => 'integer', 'default' => 0, 'required' => false ),
                "limit_value" => array( 'name' => 'LimitValue', 'datatype' => 'integer', 'default' => 0, 'required' => false ),
                "status" => array( 'name' => 'Status', 'datatype' => 'integer', 'default' => 1, 'required' => true ),
            ),
            "keys" => array( "id" ),
            "increment_key" => "id",
            "class_name" => "expLayoutsCollection",
            "name" => "explayouts_collection"
        );
    }

    static function fetchByBlock( $blockId, $asObject = true )
    {
        $list = eZPersistentObject::fetchObjectList( self::definition(), null,
            array( 'block_id' => $blockId ), null, array( 'limit' => 1 ), $asObject );
        if ( is_array( $list ) && count( $list ) > 0 )
            return $list[0];
        return false;
    }

    static function create( $blockId, $type = 'manual' )
    {
        return new self( array( 'block_id' => $blockId, 'collection_type' => $type ) );
    }
}
