<?php
class expLayoutsRuleTarget extends eZPersistentObject
{
    static function definition()
    {
        return array(
            "fields" => array(
                "id" => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "rule_id" => array( 'name' => 'RuleID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "target_type" => array( 'name' => 'TargetType', 'datatype' => 'string', 'default' => '', 'required' => true ),
                "target_value" => array( 'name' => 'TargetValue', 'datatype' => 'string', 'default' => '', 'required' => false ),
            ),
            "keys" => array( "id" ),
            "increment_key" => "id",
            "class_name" => "expLayoutsRuleTarget",
            "name" => "explayouts_rule_target"
        );
    }

    static function fetchByRule( $ruleId, $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( self::definition(), null,
            array( 'rule_id' => $ruleId ), null, null, $asObject );
    }

    static function fetchByTarget( $type, $value, $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( self::definition(), null,
            array( 'target_type' => $type, 'target_value' => $value ), null, null, $asObject );
    }

    static function create( $ruleId, $type, $value )
    {
        return new self( array(
            'rule_id' => $ruleId,
            'target_type' => $type,
            'target_value' => $value,
        ) );
    }
}
