<?php
class expLayoutsRuleCondition extends eZPersistentObject
{
    static function definition()
    {
        return array(
            "fields" => array(
                "id" => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "rule_id" => array( 'name' => 'RuleID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "condition_type" => array( 'name' => 'ConditionType', 'datatype' => 'string', 'default' => '', 'required' => true ),
                "condition_value" => array( 'name' => 'ConditionValue', 'datatype' => 'string', 'default' => '', 'required' => false ),
            ),
            "keys" => array( "id" ),
            "increment_key" => "id",
            "class_name" => "expLayoutsRuleCondition",
            "name" => "explayouts_rule_condition"
        );
    }

    static function fetchByRule( $ruleId, $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( self::definition(), null,
            array( 'rule_id' => $ruleId ), null, null, $asObject );
    }

    static function create( $ruleId, $type, $value )
    {
        return new self( array(
            'rule_id' => $ruleId,
            'condition_type' => $type,
            'condition_value' => $value,
        ) );
    }
}
