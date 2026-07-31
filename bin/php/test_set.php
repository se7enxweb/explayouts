<?php
ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

try
{
    $db = eZDB::instance();
    $queries = array(
        'SET @foo = 1',
        'SELECT @foo AS foo',
    );
    foreach ( $queries as $q )
    {
        echo 'Running: ' . $q . "\n";
        $r = $db->query( $q );
        var_dump( $r );
    }
}
catch ( Exception $e )
{
    echo 'Exception: ' . $e->getMessage() . "\n";
}
