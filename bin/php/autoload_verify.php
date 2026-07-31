<?php
$classes = array(
    'expLayoutsCoreLayoutService',
    'expLayoutsCoreRuleService',
    'expLayoutsCoreCollectionService',
    'expLayoutsCoreBlockService',
    'expLayoutsCoreZoneService',
    'expLayoutsContentBrowserProvider',
);

$missing = array();
foreach ( $classes as $class )
{
    if ( !class_exists( $class ) )
        $missing[] = $class;
}

if ( count( $missing ) > 0 )
{
    echo "Missing classes:\n" . implode( "\n", $missing ) . "\n";
    if ( isset( $script ) ) $script->setExitCode( 1 );
}
else
{
    echo "All expected classes autoloaded.\n";
}