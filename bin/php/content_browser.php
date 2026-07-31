<?php
$provider = isset( $argv[1] ) ? trim( $argv[1] ) : 'content';
$parentNodeId = isset( $argv[2] ) ? (int)$argv[2] : 2;
$search = isset( $argv[3] ) ? trim( $argv[3] ) : '';

$browser = new expLayoutsContentBrowserProvider();
$result = $browser->getItems( $provider, $parentNodeId, $search, 0, 25 );

if ( !isset( $result['items'] ) || count( $result['items'] ) === 0 )
{
    echo "No items found.\n";
    return;
}

foreach ( $result['items'] as $item )
{
    echo $item['id'] . "\t"
        . $item['class_identifier'] . "\t"
        . $item['name'] . "\t"
        . $item['url_alias'] . "\n";
}
echo "Count: " . (int)$result['count'] . "\n";