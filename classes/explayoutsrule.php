<?php
class expLayoutsRule extends eZPersistentObject
{
    static function definition()
    {
        return array(
            "fields" => array(
                "id" => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "layout_id" => array( 'name' => 'LayoutID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "priority" => array( 'name' => 'Priority', 'datatype' => 'integer', 'default' => 0, 'required' => false ),
                "enabled" => array( 'name' => 'Enabled', 'datatype' => 'integer', 'default' => 1, 'required' => true ),
            ),
            "keys" => array( "id" ),
            "increment_key" => "id",
            "class_name" => "expLayoutsRule",
            "name" => "explayouts_rule"
        );
    }

    static function fetchEnabled( $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( self::definition(), null,
            array( 'enabled' => 1 ),
            array( 'priority' => 'desc', 'id' => 'desc' ), null, $asObject );
    }

    static function fetch( $id, $asObject = true )
    {
        return eZPersistentObject::fetchObject( self::definition(), null,
            array( 'id' => (int)$id ), $asObject );
    }

    static function create( $layoutId, $priority = 0 )
    {
        return new self( array( 'layout_id' => $layoutId, 'priority' => $priority, 'enabled' => 1 ) );
    }

    function targets()
    {
        return expLayoutsRuleTarget::fetchByRule( $this->attribute( 'id' ) );
    }

    function conditions()
    {
        return expLayoutsRuleCondition::fetchByRule( $this->attribute( 'id' ) );
    }
}
