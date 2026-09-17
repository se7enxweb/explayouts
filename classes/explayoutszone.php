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
                "linked_zone_identifier" => array( 'name' => 'LinkedZoneIdentifier', 'datatype' => 'string', 'default' => null, 'required' => false ),
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

    static function fetchByLayoutAndIdentifier( $layoutId, $identifier, $status = 2, $asObject = true )
    {
        $conditions = array( 'layout_id' => (int)$layoutId, 'identifier' => (string)$identifier );
        if ( $status !== null )
            $conditions['status'] = (int)$status;

        return eZPersistentObject::fetchObject( self::definition(), null, $conditions, $asObject );
    }

    static function create( $layoutId, $identifier, $status = 1 )
    {
        $row = array( 'layout_id' => $layoutId, 'identifier' => $identifier, 'status' => $status, 'position' => 0 );
        return new self( $row );
    }

    /**
     * True when this zone inherits its blocks from a zone of a shared layout.
     *
     * Both halves of the link have to be present: a layout on its own is not
     * enough to say which of that layout's zones the blocks come from.
     */
    function isLinked()
    {
        $linkedLayoutId = (int)$this->attribute( 'linked_layout_id' );
        $linkedZone = (string)$this->attribute( 'linked_zone_identifier' );
        return $linkedLayoutId > 0 && $linkedZone !== '';
    }

    /**
     * The zone this one inherits its blocks from, or false when it owns them.
     *
     * A linked zone always shows the published state of the shared layout -
     * a draft of a shared layout is that layout's own unpublished work and
     * must not leak into the pages linking to it. Links are followed through
     * a chain, with a seen-list so a cycle cannot hang the request.
     */
    static function resolveLinkedSource( $zone )
    {
        $source = $zone;
        $seen = array();

        while ( $source instanceof self && $source->isLinked() )
        {
            $linkedLayoutId = (int)$source->attribute( 'linked_layout_id' );
            $linkedZone = (string)$source->attribute( 'linked_zone_identifier' );
            $key = $linkedLayoutId . ':' . $linkedZone;
            if ( isset( $seen[$key] ) )
                break;
            $seen[$key] = true;

            $target = self::fetchByLayoutAndIdentifier( $linkedLayoutId, $linkedZone, 2 );
            if ( !$target )
                $target = self::fetchByLayoutAndIdentifier( $linkedLayoutId, $linkedZone, null );
            if ( !$target )
                break;

            $source = $target;
        }

        return $source === $zone ? false : $source;
    }

    /**
     * The zone whose blocks this zone renders: itself, or the zone at the end
     * of its link chain.
     */
    static function resolveSource( $zone )
    {
        $source = self::resolveLinkedSource( $zone );
        return $source ? $source : $zone;
    }
}
