<?php
ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

try
{
    $db = eZDB::instance();
    echo 'DB connected: ' . ( $db ? 'yes' : 'no' ) . "\n";
    $r = $db->query( 'SELECT COUNT(*) AS c FROM explayouts_layout' );
    echo 'Query result type: ' . gettype( $r ) . "\n";
    if ( $r )
    {
        while ( $row = $r->fetch_assoc() )
            echo 'Layout count: ' . $row['c'] . "\n";
    }
    else
    {
        echo 'Query failed: ' . $db->error() . "\n";
    }
}
catch ( Exception $e )
{
    echo 'Exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
