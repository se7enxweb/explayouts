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
            "name" => "explayouts_rule_condition",
            "function_attributes" => array( "displayValue" => "displayValue", "displayItems" => "displayItems" )
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

    public function displayValue()
    {
        $type = (string)$this->attribute( 'condition_type' );
        $value = (string)$this->attribute( 'condition_value' );

        if ( in_array( $type, array( 'class', 'content_type' ) ) )
        {
            $decoded = json_decode( $value, true );
            if ( !is_array( $decoded ) || count( $decoded ) === 0 )
                $decoded = array( $value );

            $parts = array();
            foreach ( $decoded as $identifier )
            {
                $class = eZContentClass::fetchByIdentifier( (string)$identifier );
                if ( $class )
                {
                    $name = $class->attribute( 'name' );
                    $parts[] = ( $name !== '' && $name !== null ) ? $name : $class->attribute( 'identifier' );
                }
                else
                {
                    $parts[] = (string)$identifier;
                }
            }

            return implode( ', ', $parts );
        }
        elseif ( in_array( $type, array( 'siteaccess' ) ) )
        {
            $decoded = json_decode( $value, true );
            if ( is_array( $decoded ) && count( $decoded ) > 0 )
                return implode( ', ', array_map( 'trim', $decoded ) );
        }

        return $value;
    }

    public function displayItems()
    {
        $type = (string)$this->attribute( 'condition_type' );
        $value = (string)$this->attribute( 'condition_value' );

        $decoded = json_decode( $value, true );
        if ( !is_array( $decoded ) || count( $decoded ) === 0 )
            $decoded = array( $value );

        if ( in_array( $type, array( 'class', 'content_type' ) ) )
        {
            $items = array();
            foreach ( $decoded as $identifier )
            {
                $class = eZContentClass::fetchByIdentifier( (string)$identifier );
                if ( $class )
                {
                    $name = $class->attribute( 'name' );
                    $items[] = ( $name !== '' && $name !== null ) ? $name : $class->attribute( 'identifier' );
                }
                else
                {
                    $items[] = (string)$identifier;
                }
            }
            return $items;
        }

        return $decoded;
    }
}
