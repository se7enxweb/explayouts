<?php
class expLayoutsBlockParameter extends eZPersistentObject
{
    static function definition()
    {
        return array(
            "fields" => array(
                "id" => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "block_id" => array( 'name' => 'BlockID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "name" => array( 'name' => 'Name', 'datatype' => 'string', 'default' => '', 'required' => true ),
                "value" => array( 'name' => 'Value', 'datatype' => 'text', 'default' => '', 'required' => false ),
            ),
            "keys" => array( "id" ),
            "increment_key" => "id",
            "class_name" => "expLayoutsBlockParameter",
            "name" => "explayouts_block_parameter"
        );
    }

    static function fetchByBlock( $blockId, $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( self::definition(), null,
            array( 'block_id' => $blockId ), null, null, $asObject );
    }

    static function set( $blockId, $name, $value )
    {
        $existing = eZPersistentObject::fetchObjectList( self::definition(), null,
            array( 'block_id' => $blockId, 'name' => $name ), null, array( 'limit' => 1 ), true );
        if ( is_array( $existing ) && count( $existing ) > 0 )
        {
            $existing[0]->setAttribute( 'value', $value );
            $existing[0]->store();
            return $existing[0];
        }
        $row = array( 'block_id' => $blockId, 'name' => $name, 'value' => $value );
        $obj = new self( $row );
        $obj->store();
        return $obj;
    }
}
