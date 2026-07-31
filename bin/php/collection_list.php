<?php
if ( !isset( $argv[1] ) )
{
    echo "Usage: collection_list.php <block_id>\n";
    if ( isset( $script ) ) $script->setExitCode( 1 );
    return;
}

$blockId = (int)$argv[1];
$collection = expLayoutsCollection::fetchByBlock( $blockId );

if ( !$collection )
{
    echo "No collection for block.\n";
    return;
}

$items = expLayoutsCollectionItem::fetchByCollection( $collection->attribute( 'id' ) );
if ( !is_array( $items ) || count( $items ) === 0 )
{
    echo "No collection items.\n";
    return;
}

foreach ( $items as $item )
{
    if ( $item instanceof expLayoutsCollectionItem )
    {
        echo $item->attribute( 'id' ) . "\t"
            . $item->attribute( 'value_type' ) . "\t"
            . $item->attribute( 'value_id' ) . "\t"
            . $item->attribute( 'position' ) . "\t"
            . $item->attribute( 'item_type' ) . "\n";
    }
}