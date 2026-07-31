<?php
class expLayoutsBlock extends eZPersistentObject
{
    static function definition()
    {
        return array(
            "fields" => array(
                "id" => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "zone_id" => array( 'name' => 'ZoneID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "layout_id" => array( 'name' => 'LayoutID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "position" => array( 'name' => 'Position', 'datatype' => 'integer', 'default' => 0, 'required' => false ),
                "definition_identifier" => array( 'name' => 'DefinitionIdentifier', 'datatype' => 'string', 'default' => '', 'required' => true ),
                "view_type" => array( 'name' => 'ViewType', 'datatype' => 'string', 'default' => '', 'required' => false ),
                // How a block renders each of its ITEMS (overlay, listitem, line, mini,
                // standard_with_intro, standard). Distinct from view_type, which is how
                // the block itself renders. Without it every list rendered its items in
                // the same default view, unlike the reference.
                // Populated from the reference by
                // ai/bin/one/sqlite-query-update-repair-explayouts-block-data-from-nexus.sh
                "item_view_type" => array( 'name' => 'ItemViewType', 'datatype' => 'string', 'default' => '', 'required' => false ),
                "name" => array( 'name' => 'Name', 'datatype' => 'string', 'default' => '', 'required' => false ),
                "parent_id" => array( 'name' => 'ParentID', 'datatype' => 'integer', 'default' => 0, 'required' => false ),
                "placeholder" => array( 'name' => 'Placeholder', 'datatype' => 'string', 'default' => '', 'required' => false ),
                "status" => array( 'name' => 'Status', 'datatype' => 'integer', 'default' => 1, 'required' => true ),
            ),
            "keys" => array( "id" ),
            "increment_key" => "id",
            "class_name" => "expLayoutsBlock",
            "name" => "explayouts_block"
        );
    }

    static function fetch( $id, $asObject = true )
    {
        return eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => $id ), $asObject );
    }

    static function fetchByZone( $zoneId, $status = 2, $asObject = true )
    {
        $conditions = array( 'zone_id' => $zoneId );
        if ( $status !== null )
            $conditions['status'] = $status;

        return eZPersistentObject::fetchObjectList( self::definition(), null,
            $conditions,
            array( 'position' => 'asc', 'id' => 'asc' ), null, $asObject );
    }

    static function fetchChildren( $blockId, $status = 2, $asObject = true )
    {
        $conditions = array( 'parent_id' => $blockId );
        if ( $status !== null )
            $conditions['status'] = $status;

        return eZPersistentObject::fetchObjectList( self::definition(), null,
            $conditions,
            array( 'position' => 'asc', 'id' => 'asc' ), null, $asObject );
    }

    static function create( $zoneId, $layoutId, $definitionIdentifier, $name = '' )
    {
        $row = array(
            'zone_id' => $zoneId,
            'layout_id' => $layoutId,
            'definition_identifier' => $definitionIdentifier,
            'name' => $name,
            'status' => 1,
            'position' => 0,
            'view_type' => 'default',
        );
        return new self( $row );
    }

    function parameters()
    {
        $params = array();
        foreach ( expLayoutsBlockParameter::fetchByBlock( $this->attribute( 'id' ) ) as $p )
        {
            $params[$p->attribute( 'name' )] = $p->attribute( 'value' );
        }
        return $params;
    }

    function children()
    {
        return self::fetchChildren( $this->attribute( 'id' ) );
    }
}
