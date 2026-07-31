<?php
if ( !isset( $argv[1] ) )
{
    echo "Usage: layout_copy.php <layout_id>\n";
    if ( isset( $script ) ) $script->setExitCode( 1 );
    return;
}

$layoutId = (int)$argv[1];
$service = new expLayoutsCoreLayoutService();
$copy = $service->copy( $layoutId );

if ( !$copy instanceof expLayoutsLayout )
{
    echo "Copy failed.\n";
    if ( isset( $script ) ) $script->setExitCode( 1 );
    return;
}

echo "Created copy " . $copy->attribute( 'id' ) . " (" . $copy->attribute( 'identifier' ) . ")\n";