<?php
class expLayoutsCollectionItem extends eZPersistentObject
{
    static function definition()
    {
        return array(
            "fields" => array(
                "id" => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "collection_id" => array( 'name' => 'CollectionID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "position" => array( 'name' => 'Position', 'datatype' => 'integer', 'default' => 0, 'required' => false ),
                "value_type" => array( 'name' => 'ValueType', 'datatype' => 'string', 'default' => 'ez_content', 'required' => false ),
                "value_id" => array( 'name' => 'ValueID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "item_type" => array( 'name' => 'ItemType', 'datatype' => 'string', 'default' => 'manual', 'required' => false ),
            ),
            "keys" => array( "id" ),
            "increment_key" => "id",
            "class_name" => "expLayoutsCollectionItem",
            "name" => "explayouts_collection_item"
        );
    }

    static function fetchByCollection( $collectionId, $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( self::definition(), null,
            array( 'collection_id' => $collectionId ),
            array( 'position' => 'asc', 'id' => 'asc' ), null, $asObject );
    }

    static function create( $collectionId, $valueId, $valueType = 'ez_content', $itemType = 'manual' )
    {
        $maxPosition = 0;
        $list = self::fetchByCollection( $collectionId, true );
        foreach ( $list as $item )
        {
            $pos = (int)$item->attribute( 'position' );
            if ( $pos > $maxPosition )
                $maxPosition = $pos;
        }
        $row = array(
            'collection_id' => $collectionId,
            'position' => $maxPosition + 1,
            'value_type' => $valueType,
            'value_id' => $valueId,
            'item_type' => $itemType,
        );
        return new self( $row );
    }
}
