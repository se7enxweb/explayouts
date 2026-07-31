<?php
require_once __DIR__ . '/../../classes/explayoutsfixtures.php';

ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

try
{
    $result = expLayoutsFixtures::installTestData();

    if ( isset( $result['error'] ) )
    {
        echo "Error: " . $result['error'] . "\n";
        if ( isset( $script ) )
            $script->setExitCode( 1 );
    }
    else
    {
        echo $result['message'] . "\n";
    }
}
catch ( Exception $e )
{
    echo 'Exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    if ( isset( $script ) )
        $script->setExitCode( 1 );
}
