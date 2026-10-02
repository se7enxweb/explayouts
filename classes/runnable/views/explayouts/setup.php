<?php
/**
 * The code of extension/explayouts/modules/explayouts/setup.php, moved into a class (#207 stage 1). The file extension/explayouts/modules/explayouts/setup.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\View\Extension\Explayouts\Explayouts
{

class Setup extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $module = $Params['Module'];

        if ( !\eZUser::currentUser()->hasAccessTo( 'explayouts', 'edit' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
        }

        $db = \eZDB::instance();
        $dbType = strtolower( $db->databaseName() );

        $message = '';
        $error = '';
        $schemaFile = false;

        switch ( $dbType )
        {
            case 'mysql':
            case 'mysqli':
                $schemaFile = 'extension/explayouts/sql/mysql/schema.sql';
                break;

            case 'postgresql':
            case 'pgsql':
                $schemaFile = 'extension/explayouts/sql/postgresql/schema.sql';
                break;

            case 'sqlite':
            case 'sqlite3':
                $schemaFile = 'extension/explayouts/sql/sqlite/schema.sql';
                break;

            case 'mongo':
            case 'mongodb':
                $schemaFile = 'extension/explayouts/sql/mongodb/schema.json';
                break;

            default:
                $error = \ezpI18n::tr( 'design/admin/explayouts/setup', 'Unsupported database type: %type', null, array( '%type' => $dbType ) );
        }

        if ( $schemaFile && $http->hasPostVariable( 'InstallSchema' ) )
        {
            if ( in_array( $dbType, array( 'mongo', 'mongodb' ) ) )
            {
                $result = \expLayoutsMongoInstaller::install( \eZSys::rootDir() . '/' . $schemaFile );
                if ( $result['success'] )
                    $message = \ezpI18n::tr( 'design/admin/explayouts/setup', 'MongoDB collections created: %created, indexes: %indexes', null, array( '%created' => $result['created'], '%indexes' => $result['indexes'] ) );
                else
                    $error = $result['error'];
            }
            else
            {
                $result = \expLayoutsInstall::runSqlFile( $schemaFile );
                if ( isset( $result['error'] ) )
                    $error = $result['error'];
                else
                    $message = $result['message'];
            }
        }

        if ( $http->hasPostVariable( 'InstallBaseData' ) )
        {
            $result = \expLayoutsFixtures::installBaseData();
            if ( isset( $result['error'] ) )
                $error = $result['error'];
            else
                $message = $result['message'];
        }

        if ( $http->hasPostVariable( 'InstallTestData' ) )
        {
            $result = \expLayoutsFixtures::installTestData();
            if ( isset( $result['error'] ) )
                $error = $result['error'];
            else
                $message = $result['message'];
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'db_type', $dbType );
        $tpl->setVariable( 'schema_file', $schemaFile );
        $tpl->setVariable( 'message', $message );
        $tpl->setVariable( 'error', $error );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:explayouts/setup.tpl' );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'explayouts/setup', 'Setup' ) ) );
        return $this->viewResult( isset( $Result ) ? $Result : null,  $Result );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
