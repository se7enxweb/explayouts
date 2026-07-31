<?php
if ( !isset( $argv[1] ) )
{
    echo "Usage: layout_delete.php <layout_id>\n";
    if ( isset( $script ) ) $script->setExitCode( 1 );
    return;
}

$layoutId = (int)$argv[1];
$service = new expLayoutsCoreLayoutService();
$result = $service->delete( $layoutId );

if ( $result )
    echo "Layout " . $layoutId . " deleted.\n";
else
{
    echo "Delete failed.\n";
    if ( isset( $script ) ) $script->setExitCode( 1 );
}