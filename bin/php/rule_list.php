<?php
if ( !isset( $argv[1] ) )
{
    echo "Usage: rule_list.php <layout_id>\n";
    if ( isset( $script ) ) $script->setExitCode( 1 );
    return;
}

$layoutId = (int)$argv[1];
$service = new expLayoutsCoreRuleService();
$rules = $service->listByLayout( $layoutId );

if ( !is_array( $rules ) || count( $rules ) === 0 )
{
    echo "No rules found.\n";
    return;
}

foreach ( $rules as $rule )
{
    if ( $rule instanceof expLayoutsRule )
    {
        echo $rule->attribute( 'id' ) . "\t"
            . $rule->attribute( 'priority' ) . "\t"
            . ( $rule->attribute( 'enabled' ) ? 'enabled' : 'disabled' ) . "\n";
    }
}