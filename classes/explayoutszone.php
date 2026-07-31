<?php
class expLayoutsZone extends eZPersistentObject
{
    static function definition()
    {
        return array(
            "fields" => array(
                "id" => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "layout_id" => array( 'name' => 'LayoutID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "identifier" => array( 'name' => 'Identifier', 'datatype' => 'string', 'default' => '', 'required' => true ),
                "linked_layout_id" => array( 'name' => 'LinkedLayoutID', 'datatype' => 'integer', 'default' => null, 'required' => false ),
                "status" => array( 'name' => 'Status', 'datatype' => 'integer', 'default' => 1, 'required' => true ),
                "position" => array( 'name' => 'Position', 'datatype' => 'integer', 'default' => 0, 'required' => false ),
            ),
            "keys" => array( "id" ),
            "increment_key" => "id",
            "class_name" => "expLayoutsZone",
            "name" => "explayouts_zone"
        );
    }

    static function fetch( $id, $asObject = true )
    {
        return eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => $id ), $asObject );
    }

    static function fetchByLayout( $layoutId, $status = 2, $asObject = true )
    {
        $conditions = array( 'layout_id' => $layoutId );
        if ( $status !== null )
            $conditions['status'] = $status;

        return eZPersistentObject::fetchObjectList( self::definition(), null,
            $conditions,
            array( 'position' => 'asc', 'id' => 'asc' ), null, $asObject );
    }

    static function create( $layoutId, $identifier, $status = 1 )
    {
        $row = array( 'layout_id' => $layoutId, 'identifier' => $identifier, 'status' => $status, 'position' => 0 );
        return new self( $row );
    }
}
