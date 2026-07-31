<?php
if ( !isset( $argv[1] ) )
{
    echo "Usage: block_list.php <layout_id>\n";
    if ( isset( $script ) ) $script->setExitCode( 1 );
    return;
}

$layoutId = (int)$argv[1];
$layout = expLayoutsLayout::fetch( $layoutId );

if ( !$layout instanceof expLayoutsLayout )
{
    echo "Layout not found.\n";
    if ( isset( $script ) ) $script->setExitCode( 1 );
    return;
}

$zones = expLayoutsZone::fetchByLayout( $layoutId, (int)$layout->attribute( 'status' ) );
foreach ( $zones as $zone )
{
    $blocks = expLayoutsBlock::fetchByZone( $zone->attribute( 'id' ), (int)$layout->attribute( 'status' ) );
    foreach ( $blocks as $block )
    {
        echo $zone->attribute( 'identifier' ) . "\t"
            . $block->attribute( 'id' ) . "\t"
            . $block->attribute( 'definition_identifier' ) . "\t"
            . $block->attribute( 'name' ) . "\t"
            . $block->attribute( 'view_type' ) . "\n";
    }
}