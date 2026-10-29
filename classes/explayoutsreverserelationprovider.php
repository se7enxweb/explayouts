<?php
/**
 * The Layouts blocks that name content a removal or a hide takes away, for the kernel's reverse relation check
 * (content.ini [ReverseRelationSettings] ProviderClassList[], Exponential\Service\ReverseRelationCheck).
 *
 * A collection item of a block stores a node id in value_id, whatever its value_type (the manual query handler
 * fetches nodes). A published block whose collection names a node inside the selected subtrees loses that item
 * when the node goes, even when the object keeps a location elsewhere, so every such node counts.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package explayouts
 */

class expLayoutsReverseRelationProvider
{
    /** The status of a published layout, block and collection */
    const STATUS_PUBLISHED = 2;

    /**
     * The blocks of published layouts whose collections name a node inside the subtrees.
     *
     * @param string[] $paths path strings of the selected nodes ('/1/2/42/'), digits and slashes only
     * @param int $limit at most this many rows are returned; the total counts all
     * @return array|false array( 'total' => int, 'rows' => array( array( 'to_node_id', 'to_object_id', 'to_name',
     *         'to_path', 'label', 'type', 'url', 'readable' ) ) ), false when the tables could not be read
     */
    public static function reverseRelations( array $paths, $limit )
    {
        $or = array();
        foreach ( $paths as $path )
        {
            // only digits and slashes go into LIKE
            if ( is_string( $path ) && preg_match( '#^(/[0-9]+)+/$#', $path ) )
                $or[] = "t.path_string LIKE '$path%'";
        }
        if ( !$or )
            return array( 'total' => 0, 'rows' => array() );

        $published = (int) self::STATUS_PUBLISHED;
        $from = 'FROM explayouts_collection_item i'
              . " JOIN explayouts_collection c ON c.id = i.collection_id AND c.status = $published"
              . " JOIN explayouts_block b ON b.id = c.block_id AND b.status = $published"
              . " JOIN explayouts_layout l ON l.id = b.layout_id AND l.status = $published"
              . ' JOIN ezcontentobject_tree t ON t.node_id = i.value_id'
              . ' WHERE ( ' . implode( ' OR ', $or ) . ' )';
        $db = eZDB::instance();
        try
        {
            $count = $db->arrayQuery( "SELECT COUNT(*) AS cnt $from" );
            if ( !is_array( $count ) )
                return false;
            $total = $count ? (int) $count[0]['cnt'] : 0;
            if ( $total === 0 )
                return array( 'total' => 0, 'rows' => array() );
            $rows = $db->arrayQuery( 'SELECT i.value_id AS node_id, t.contentobject_id AS object_id, t.path_string AS path_string,'
                                     . ' b.id AS block_id, b.name AS block_name, b.definition_identifier AS definition,'
                                     . " l.id AS layout_id, l.name AS layout_name $from"
                                     . ' ORDER BY l.id, b.id, i.position',
                                     array( 'limit' => max( 1, (int) $limit ) ) );
        }
        catch ( Exception $e )
        {
            eZDebug::writeError( get_class( $e ) . ': ' . $e->getMessage(), __METHOD__ );
            return false;
        }
        if ( !is_array( $rows ) )
            return false;

        $access = eZUser::currentUser()->hasAccessTo( 'explayouts', 'read' );
        $readable = is_array( $access ) && isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
        $type = ezpI18n::tr( 'design/admin/content/reverserelations', 'Layouts block' );

        // the names of the nodes, in one query
        $names = array();
        $objectIDs = array();
        foreach ( $rows as $row )
            $objectIDs[(int) $row['object_id']] = true;
        if ( $objectIDs )
        {
            $objects = eZContentObject::fetchIDArray( array_keys( $objectIDs ) );
            foreach ( is_array( $objects ) ? $objects : array() as $object )
                if ( $object instanceof eZContentObject )
                    $names[(int) $object->attribute( 'id' )] = (string) $object->attribute( 'name' );
        }

        $result = array();
        foreach ( $rows as $row )
        {
            $block = trim( (string) $row['block_name'] ) !== '' ? (string) $row['block_name'] : (string) $row['definition'];
            $objectID = (int) $row['object_id'];
            $result[] = array( 'to_node_id' => (int) $row['node_id'],
                               'to_object_id' => $objectID,
                               'to_name' => isset( $names[$objectID] ) ? $names[$objectID] : '',
                               'to_path' => (string) $row['path_string'],
                               'label' => $row['layout_name'] . ': ' . $block,
                               'type' => $type,
                               'url' => '/explayouts_ui_api/app#layout/' . (int) $row['layout_id'],
                               'readable' => $readable );
        }
        return array( 'total' => $total, 'rows' => $result );
    }
}
