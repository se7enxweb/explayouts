<?php
class expLayoutsCollectionQuery extends eZPersistentObject
{
    static function definition()
    {
        return array(
            "fields" => array(
                "id" => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "collection_id" => array( 'name' => 'CollectionID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                "query_type" => array( 'name' => 'QueryType', 'datatype' => 'string', 'default' => '', 'required' => true ),
                "parameters" => array( 'name' => 'Parameters', 'datatype' => 'text', 'default' => '', 'required' => false ),
            ),
            "keys" => array( "id" ),
            "increment_key" => "id",
            "class_name" => "expLayoutsCollectionQuery",
            "name" => "explayouts_collection_query"
        );
    }

    static function fetchByCollection( $collectionId, $asObject = true )
    {
        $list = eZPersistentObject::fetchObjectList( self::definition(), null,
            array( 'collection_id' => (int)$collectionId ), null, array( 'limit' => 1 ), $asObject );
        if ( is_array( $list ) && count( $list ) > 0 )
            return $list[0];
        return false;
    }

    static function set( $collectionId, $queryType, $parameters )
    {
        $existing = self::fetchByCollection( $collectionId, true );
        if ( $existing )
        {
            $existing->setAttribute( 'query_type', $queryType );
            $existing->setAttribute( 'parameters', $parameters );
            $existing->store();
            return $existing;
        }

        $obj = new self( array(
            'collection_id' => (int)$collectionId,
            'query_type' => $queryType,
            'parameters' => $parameters,
        ) );
        $obj->store();
        return $obj;
    }

    static function removeByCollection( $collectionId )
    {
        $existing = self::fetchByCollection( $collectionId, true );
        if ( $existing )
            $existing->remove();
    }
}
