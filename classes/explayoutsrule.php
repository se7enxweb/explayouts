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

    static function create( $layoutId, $priority = null )
    {
        if ( $priority === null )
            $priority = self::nextPriority();

        return new self( array( 'layout_id' => $layoutId, 'priority' => (int)$priority, 'enabled' => 1 ) );
    }

    /**
     * Priority for a newly created rule: above every existing one.
     *
     * Rules are evaluated highest priority first and the first match wins
     * (see fetchEnabled() and expLayoutsResolver::resolve()). The site's
     * catch-all rule targets path_info_prefix '/' at priority 10, so it
     * matches every path - which means a new rule created below it can never
     * be reached, and a freshly mapped layout silently does nothing.
     *
     * The reference assigns lowest-priority-minus-10 here, putting new rules
     * at the bottom of the list; with a catch-all sitting at the bottom that
     * makes every new mapping dead on arrival. Going above the highest instead
     * means a rule the editor just created actually applies.
     *
     * Steps of 10 leave room to insert rules in between by hand.
     */
    static function nextPriority()
    {
        $rows = eZPersistentObject::fetchObjectList( self::definition(),
            array( 'priority' ), null,
            array( 'priority' => 'desc' ), array( 'limit' => 1 ), false );

        $highest = ( is_array( $rows ) && isset( $rows[0]['priority'] ) )
            ? (int)$rows[0]['priority']
            : 0;

        return $highest + 10;
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
