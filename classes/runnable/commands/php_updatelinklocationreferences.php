<?php
/**
 * The code of extension/explayouts/bin/php/updatelinklocationreferences.php, moved into a class (#207 stage 1). The file extension/explayouts/bin/php/updatelinklocationreferences.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 * @description Convert block links from ibexa-location://<id> to exp-remote-location://<remote id>
 */
/*
 * The original header of extension/explayouts/bin/php/updatelinklocationreferences.php:
 *
 *
 * Converts the block links stored as ibexa-location://<id> to
 * exp-remote-location://<remote id>, in every status: published, draft and
 * archived.
 *
 * ibexa-location://<id> names a location of the reference installation, which
 * is mapped to a node of this one through [NexusNodeMap] in explayouts.ini
 * (expLayoutsDynamicCollection::remapNodeId()). The script resolves each one
 * exactly that way and stores the remote id of the node it lands on, so the
 * rendered link does not change. A remote id is used rather than a node id
 * because node ids are handed out at install time and differ between
 * installations; remote ids come with the content package.
 *
 * Run it once after upgrading explayouts, from the installation's root:
 *
 *   php extension/explayouts/bin/php/updatelinklocationreferences.php --dry-run
 *   php extension/explayouts/bin/php/updatelinklocationreferences.php
 *
 * Add -s <siteaccess> for an installation whose database is set per
 * siteaccess. Running it again changes nothing. Until it has run, the old
 * values are still read, so nothing breaks in between. A reference that
 * resolves to no node is left as it is and reported. Afterwards, clear the
 * caches that hold rendered blocks:
 *
 *   php bin/php/ezcache.php --clear-id=content,template-block --allow-root-user
 *
 */

namespace Exponential\Command\Extension\Explayouts
{

class Updatelinklocationreferences extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'cli', 'db', 'dryRun', 'id', 'left', 'newScheme', 'newValue', 'oldScheme', 'options', 'result', 'row', 'rows', 'script', 'total', 'unresolved', 'updates', 'value' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = $this->cli();
        $script = $this->script( array(
            'description' => "Converts the block links stored as ibexa-location://<id> to exp-remote-location://<remote id>.",
            'use-session' => false,
            'use-modules' => false,
            'use-extensions' => true,
        ) );
        $options = $this->startup( '[dry-run]', '', array( 'dry-run' => 'Report what would be converted, change nothing.' ) );

        $oldScheme = 'ibexa-location://';
        $newScheme = 'exp-remote-location://';
        $dryRun = (bool)$options['dry-run'];
        $db = \eZDB::instance();

        $rows = $db->arrayQuery( "SELECT id, block_id, name, value FROM explayouts_block_parameter WHERE value LIKE '%" . $db->escapeString( $oldScheme ) . "%'" );
        if ( !is_array( $rows ) )
        {
            $cli->error( 'FAIL could not read explayouts_block_parameter' );
            $script->shutdown( 1 );
        }

        $updates = array();
        $unresolved = 0;
        foreach ( $rows as $row )
        {
            $row = array_change_key_case( $row, CASE_LOWER );
            $value = (string)$row['value'];
            $newValue = preg_replace_callback( '#ibexa-location://(\d+)#', function ( $m ) use ( $newScheme, $cli, $row, &$unresolved )
            {
                $node = \expLayoutsLinkParameter::referencedNode( $m[0] );
                $remoteId = $node ? (string)$node->attribute( 'remote_id' ) : '';
                if ( $remoteId === '' )
                {
                    $unresolved++;
                    $cli->warning( sprintf( 'WARN parameter %d (block %d, %s): %s names no node; left as it is',
                                            $row['id'], $row['block_id'], $row['name'], $m[0] ) );
                    return $m[0];
                }
                return $newScheme . $remoteId;
            }, $value );

            if ( $newValue !== $value )
            {
                $updates[(int)$row['id']] = $newValue;
                $cli->output( sprintf( '%s parameter %d (block %d, %s): %s -> %s', $dryRun ? 'would convert' : 'converting',
                                       $row['id'], $row['block_id'], $row['name'], $value, $newValue ) );
            }
        }

        if ( !$updates )
        {
            $cli->output( $unresolved ? "PASS nothing to convert; $unresolved references name no node and were left as they are"
                                      : 'PASS nothing to convert: no block link uses ' . $oldScheme );
            $script->shutdown( 0 );
        }

        $total = count( $updates );
        if ( $dryRun )
        {
            $cli->output( "DRY $total parameters would be converted; nothing was changed" );
            $script->shutdown( 0 );
        }

        $db->begin();
        foreach ( $updates as $id => $newValue )
        {
            $result = $db->query( "UPDATE explayouts_block_parameter SET value = '" . $db->escapeString( $newValue ) . "' WHERE id = " . (int)$id );
            if ( $result === false )
            {
                $db->rollback();
                $cli->error( "FAIL converting parameter $id; nothing was changed" );
                $script->shutdown( 1 );
            }
        }
        $db->commit();

        $left = $db->arrayQuery( "SELECT COUNT(*) AS parameter_count FROM explayouts_block_parameter WHERE value LIKE '%" . $db->escapeString( $oldScheme ) . "%'" );
        $left = is_array( $left ) && $left ? (int)current( array_change_key_case( $left[0], CASE_LOWER ) ) : -1;
        if ( $left !== $unresolved )
        {
            $cli->error( "FAIL $left parameters still use $oldScheme, $unresolved expected (those naming no node)" );
            $script->shutdown( 1 );
        }

        $cli->output( "PASS $total parameters converted" . ( $unresolved ? ", $unresolved unresolvable references left as they are" : '' )
                      . '. Clear the content and template-block caches now.' );
        $script->shutdown( 0 );
    }
}

}
