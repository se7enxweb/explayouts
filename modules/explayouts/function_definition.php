<?php
$FunctionList = array();

$FunctionList['layout'] = array(
    'name' => 'layout',
    'operation_types' => array( 'read' ),
    'call_method' => array(
        'include_file' => 'extension/explayouts/modules/explayouts/functioncollection.php',
        'class' => 'expLayoutsFunctionCollection',
        'method' => 'fetchLayout'
    ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'identifier', 'type' => 'string', 'required' => true ),
    )
);

$FunctionList['resolve_layout'] = array(
    'name' => 'resolve_layout',
    'operation_types' => array( 'read' ),
    'call_method' => array(
        'include_file' => 'extension/explayouts/modules/explayouts/functioncollection.php',
        'class' => 'expLayoutsFunctionCollection',
        'method' => 'resolveLayout'
    ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'path', 'type' => 'string', 'required' => false ),
    )
);

$FunctionList['resolve_layout_for_node'] = array(
    'name' => 'resolve_layout_for_node',
    'operation_types' => array( 'read' ),
    'call_method' => array(
        'include_file' => 'extension/explayouts/modules/explayouts/functioncollection.php',
        'class' => 'expLayoutsFunctionCollection',
        'method' => 'resolveLayoutForNode'
    ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'node_id', 'type' => 'integer', 'required' => true ),
    )
);

$FunctionList['rules_for_node'] = array(
    'name' => 'rules_for_node',
    'operation_types' => array( 'read' ),
    'call_method' => array(
        'include_file' => 'extension/explayouts/modules/explayouts/functioncollection.php',
        'class' => 'expLayoutsFunctionCollection',
        'method' => 'rulesForNode'
    ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'node_id', 'type' => 'integer', 'required' => true ),
    )
);
