<?php
$http = eZHTTPTool::instance();
$module = $Params['Module'];

if ( !eZUser::currentUser()->hasAccessTo( 'explayouts', 'edit' ) )
{
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$db = eZDB::instance();
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
        $error = 'Unsupported database type: ' . $dbType;
}

if ( $schemaFile && $http->hasPostVariable( 'InstallSchema' ) )
{
    if ( in_array( $dbType, array( 'mongo', 'mongodb' ) ) )
    {
        $result = expLayoutsMongoInstaller::install( eZSys::rootDir() . '/' . $schemaFile );
        if ( $result['success'] )
            $message = 'MongoDB collections created: ' . $result['created'] . ', indexes: ' . $result['indexes'];
        else
            $error = $result['error'];
    }
    else
    {
        $result = expLayoutsInstall::runSqlFile( $schemaFile );
        if ( isset( $result['error'] ) )
            $error = $result['error'];
        else
            $message = $result['message'];
    }
}

if ( $http->hasPostVariable( 'InstallBaseData' ) )
{
    $result = expLayoutsFixtures::installBaseData();
    if ( isset( $result['error'] ) )
        $error = $result['error'];
    else
        $message = $result['message'];
}

if ( $http->hasPostVariable( 'InstallTestData' ) )
{
    $result = expLayoutsFixtures::installTestData();
    if ( isset( $result['error'] ) )
        $error = $result['error'];
    else
        $message = $result['message'];
}

$tpl = eZTemplate::factory();
$tpl->setVariable( 'db_type', $dbType );
$tpl->setVariable( 'schema_file', $schemaFile );
$tpl->setVariable( 'message', $message );
$tpl->setVariable( 'error', $error );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:explayouts/setup.tpl' );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'explayouts/setup', 'Setup' ) ) );
return $Result;
