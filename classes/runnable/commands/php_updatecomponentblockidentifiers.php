<?php
/**
 * The code of extension/explayouts/bin/php/updatecomponentblockidentifiers.php, moved into a class (#207 stage 1). The file extension/explayouts/bin/php/updatecomponentblockidentifiers.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 * @description Rename stored component blocks from ibexa_component_<type> to exp_component_<type>
 */
/*
 * The original header of extension/explayouts/bin/php/updatecomponentblockidentifiers.php:
 *
 *
 * Renames the content component blocks stored under their old identifiers,
 * ibexa_component_<type>, to exp_component_<type> (hero, features, about,
 * logos, quote, lead), in every status: published, draft and archived.
 *
 * Run it once after upgrading explayouts, from the installation's root:
 *
 *   php extension/explayouts/bin/php/updatecomponentblockidentifiers.php --dry-run
 *   php extension/explayouts/bin/php/updatecomponentblockidentifiers.php
 *
 * Add -s <siteaccess> for an installation whose database is set per
 * siteaccess. Running it again changes nothing. Until it has run, the old
 * names still find their definitions, so nothing breaks in between.
 * Afterwards, clear the caches that hold rendered blocks (the layout resolver
 * cache holds only layout ids and can stay):
 *
 *   php bin/php/ezcache.php --clear-id=content,template-block --allow-root-user
 *
 */

namespace Exponential\Command\Extension\Explayouts
{

class Updatecomponentblockidentifiers extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'cli', 'db', 'dryRun', 'identifier', 'left', 'newPrefix', 'old', 'oldPrefix', 'options', 'rename', 'renames', 'result', 'row', 'rows', 'script', 'total' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = \eZCLI::instance();
        $script = \eZScript::instance( array(
            'description' => "Renames the stored component blocks from ibexa_component_<type> to exp_component_<type>.",
            'use-session' => false,
            'use-modules' => false,
            'use-extensions' => true,
        ) );
        $script->startup();
        $options = $script->getOptions( '[dry-run]', '', array( 'dry-run' => 'Report what would be renamed, change nothing.' ) );
        $script->initialize();

        $oldPrefix = 'ibexa_component_';
        $newPrefix = 'exp_component_';
        $dryRun = (bool)$options['dry-run'];
        $db = \eZDB::instance();

        $rows = $db->arrayQuery( "SELECT definition_identifier, COUNT(*) AS block_count FROM explayouts_block GROUP BY definition_identifier" );
        if ( !is_array( $rows ) )
        {
            $cli->error( 'FAIL could not read explayouts_block' );
            $script->shutdown( 1 );
        }

        $renames = array();
        foreach ( $rows as $row )
        {
            $row = array_change_key_case( $row, CASE_LOWER );
            $identifier = (string)$row['definition_identifier'];
            if ( strpos( $identifier, $oldPrefix ) === 0 )
                $renames[$identifier] = array( $newPrefix . substr( $identifier, strlen( $oldPrefix ) ), (int)$row['block_count'] );
        }

        if ( !$renames )
        {
            $cli->output( 'PASS nothing to rename: no block uses an ' . $oldPrefix . '* identifier' );
            $script->shutdown( 0 );
        }

        $total = 0;
        foreach ( $renames as $old => $rename )
        {
            $cli->output( sprintf( '%s %d blocks: %s -> %s', $dryRun ? 'would rename' : 'renaming', $rename[1], $old, $rename[0] ) );
            $total += $rename[1];
        }

        if ( $dryRun )
        {
            $cli->output( "DRY $total blocks would be renamed; nothing was changed" );
            $script->shutdown( 0 );
        }

        $db->begin();
        foreach ( $renames as $old => $rename )
        {
            $result = $db->query( "UPDATE explayouts_block SET definition_identifier = '" . $db->escapeString( $rename[0] ) . "'"
                . " WHERE definition_identifier = '" . $db->escapeString( $old ) . "'" );
            if ( $result === false )
            {
                $db->rollback();
                $cli->error( "FAIL renaming $old; nothing was changed" );
                $script->shutdown( 1 );
            }
        }
        $db->commit();

        $left = $db->arrayQuery( "SELECT COUNT(*) AS block_count FROM explayouts_block WHERE definition_identifier LIKE '" . $oldPrefix . "%'" );
        $left = is_array( $left ) && $left ? (int)current( array_change_key_case( $left[0], CASE_LOWER ) ) : -1;
        if ( $left !== 0 )
        {
            $cli->error( "FAIL $left blocks still use an $oldPrefix* identifier" );
            $script->shutdown( 1 );
        }

        $cli->output( "PASS $total blocks renamed. Clear the content and template-block caches now." );
        $script->shutdown( 0 );
    }
}

}
