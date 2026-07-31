<?php
ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

$path = __DIR__ . '/../../sql/mysql/test_data.sql';
if ( !file_exists( $path ) )
{
    echo "File not found: $path\n";
    return;
}

$sql = file_get_contents( $path );
$queries = array_filter( array_map( 'trim', preg_split( '/;[\s]*$/m', $sql ) ) );

try
{
    $db = eZDB::instance();
    $executed = 0;
    $failed = 0;
    foreach ( $queries as $query )
    {
        if ( $query === '' )
            continue;
        echo 'Q: ' . substr( $query, 0, 80 ) . "...\n";
        $r = $db->query( $query );
        if ( $r === false )
        {
            $failed++;
            $err = $db->error();
            echo 'FAIL: ' . ( $err ? $err : 'unknown' ) . "\n";
        }
        else
        {
            $executed++;
            echo "OK\n";
        }
    }
    echo "Executed $executed, failed $failed\n";
}
catch ( Exception $e )
{
    echo 'Exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
