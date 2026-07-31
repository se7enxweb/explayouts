<?php
class expLayoutsLayout extends eZPersistentObject
{
    static function definition()
    {
        return array(
            "fields" => array(
                "id" => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "identifier" => array( 'name' => 'Identifier', 'datatype' => 'string', 'default' => '', 'required' => true ),
                "name" => array( 'name' => 'Name', 'datatype' => 'string', 'default' => '', 'required' => false ),
                "layout_type" => array( 'name' => 'LayoutType', 'datatype' => 'string', 'default' => '', 'required' => false ),
                "status" => array( 'name' => 'Status', 'datatype' => 'integer', 'default' => 1, 'required' => true ),
                "created" => array( 'name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => false ),
                "modified" => array( 'name' => 'Modified', 'datatype' => 'integer', 'default' => 0, 'required' => false ),
            ),
            "keys" => array( "id" ),
            "increment_key" => "id",
            "sort" => array( "id" => "asc" ),
            "class_name" => "expLayoutsLayout",
            "name" => "explayouts_layout"
        );
    }

    static function create( $identifier, $name = '', $layoutType = '' )
    {
        $row = array(
            'identifier' => $identifier,
            'name' => $name,
            'layout_type' => $layoutType,
            'status' => 1,
            'created' => time(),
            'modified' => time(),
        );
        return new self( $row );
    }

    static function fetch( $id, $asObject = true )
    {
        return eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int)$id ), $asObject );
    }

    static function fetchByIdentifier( $identifier, $status = 2, $asObject = true )
    {
        $list = eZPersistentObject::fetchObjectList( self::definition(), null,
            array( 'identifier' => $identifier, 'status' => $status ),
            null, array( 'limit' => 1 ), $asObject );
        if ( is_array( $list ) && count( $list ) > 0 )
            return $list[0];
        return false;
    }

    static function fetchList( $status = false, $asObject = true )
    {
        $conds = array();
        if ( $status !== false )
            $conds['status'] = $status;
        return eZPersistentObject::fetchObjectList( self::definition(), null, $conds,
            array( 'id' => 'desc' ), null, $asObject );
    }

    static function removeDraft( $identifier, $excludeId = false )
    {
        $db = eZDB::instance();
        $sql = "DELETE FROM explayouts_layout WHERE identifier = '" . $db->escapeString( $identifier ) . "' AND status = 1";
        if ( $excludeId !== false )
            $sql .= " AND id != " . (int)$excludeId;
        $db->query( $sql );
    }

    function publish()
    {
        if ( $this->attribute( 'status' ) == 2 )
            return;

        $identifier = $this->attribute( 'identifier' );
        self::removeDraft( $identifier, (int)$this->attribute( 'id' ) );

        $published = self::fetchByIdentifier( $identifier, 2 );
        $publishedId = $published ? (int)$published->attribute( 'id' ) : 0;
        if ( $published && $publishedId !== (int)$this->attribute( 'id' ) )
            $published->remove();

        $this->setAttribute( 'status', 2 );
        $this->setAttribute( 'modified', time() );
        $this->store();

        if ( $publishedId > 0 )
        {
            eZDB::instance()->query( 'UPDATE explayouts_rule SET layout_id = ' . (int)$this->attribute( 'id' ) . ' WHERE layout_id = ' . $publishedId );
        }

        foreach ( expLayoutsZone::fetchByLayout( (int)$this->attribute( 'id' ), null ) as $zone )
        {
            $zone->setAttribute( 'status', 2 );
            $zone->setAttribute( 'modified', time() );
            $zone->store();
            foreach ( expLayoutsBlock::fetchByZone( (int)$zone->attribute( 'id' ), null ) as $block )
            {
                $block->setAttribute( 'status', 2 );
                $block->setAttribute( 'modified', time() );
                $block->store();
            }
        }
    }
}
