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

$FunctionList['resolve_admin_layout'] = array(
    'name' => 'resolve_admin_layout',
    'operation_types' => array( 'read' ),
    'call_method' => array(
        'include_file' => 'extension/explayouts/modules/explayouts/functioncollection.php',
        'class' => 'expLayoutsFunctionCollection',
        'method' => 'resolveAdminLayout'
    ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'module', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'view', 'type' => 'string', 'required' => false, 'default' => false ),
    )
);

$FunctionList['admin_layout_cache_key'] = array(
    'name' => 'admin_layout_cache_key',
    'operation_types' => array( 'read' ),
    'call_method' => array(
        'include_file' => 'extension/explayouts/modules/explayouts/functioncollection.php',
        'class' => 'expLayoutsFunctionCollection',
        'method' => 'adminLayoutCacheKey'
    ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'module', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'view', 'type' => 'string', 'required' => false, 'default' => false ),
    )
);

$FunctionList['admin_layouts_enabled'] = array(
    'name' => 'admin_layouts_enabled',
    'operation_types' => array( 'read' ),
    'call_method' => array(
        'include_file' => 'extension/explayouts/modules/explayouts/functioncollection.php',
        'class' => 'expLayoutsFunctionCollection',
        'method' => 'adminLayoutsEnabled'
    ),
    'parameter_type' => 'standard',
    'parameters' => array()
);

$FunctionList['layout_summary_for_node'] = array(
    'name' => 'layout_summary_for_node',
    'operation_types' => array( 'read' ),
    'call_method' => array(
        'include_file' => 'extension/explayouts/modules/explayouts/functioncollection.php',
        'class' => 'expLayoutsFunctionCollection',
        'method' => 'layoutSummaryForNode'
    ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'node_id', 'type' => 'integer', 'required' => true ),
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
