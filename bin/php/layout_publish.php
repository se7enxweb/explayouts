<?php
if ( !isset( $argv[1] ) )
{
    echo "Usage: layout_publish.php <layout_id>\n";
    if ( isset( $script ) ) $script->setExitCode( 1 );
    return;
}

$layoutId = (int)$argv[1];
$service = new expLayoutsCoreLayoutService();
$published = $service->publish( $layoutId );

if ( $published instanceof expLayoutsLayout )
    echo "Published layout " . $published->attribute( 'id' ) . " (" . $published->attribute( 'identifier' ) . ")\n";
else
{
    echo "Publish failed.\n";
    if ( isset( $script ) ) $script->setExitCode( 1 );
}