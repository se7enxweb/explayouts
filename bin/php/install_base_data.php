<?php
require_once __DIR__ . '/../../classes/explayoutsfixtures.php';

$result = expLayoutsFixtures::installBaseData();

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
