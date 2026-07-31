<?php
$Module = array( 'name' => 'explayouts',
                 'variable_params' => true );

$ViewList = array();

$ViewList['dashboard'] = array(
    'script' => 'dashboard.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array()
);

$ViewList['setup'] = array(
    'script' => 'setup.php',
    'functions' => array( 'edit' ),
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array()
);

$ViewList['template_editor'] = array(
    'script' => 'template_editor.php',
    'functions' => array( 'read', 'edit' ),
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array( 'FilePath' )
);

$ViewList['layout_list'] = array(
    'script' => 'layout_list.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array()
);

$ViewList['layout_edit'] = array(
    'script' => 'layout_edit.php',
    'functions' => array( 'edit' ),
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array( 'LayoutID' )
);

$ViewList['layout_preview'] = array(
    'script' => 'preview.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array( 'LayoutID', 'Status' )
);

$ViewList['block_edit'] = array(
    'script' => 'block_edit.php',
    'functions' => array( 'edit' ),
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array( 'BlockID' )
);

$ViewList['rule_list'] = array(
    'script' => 'rule_list.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array()
);

$ViewList['rule_edit'] = array(
    'script' => 'rule_edit.php',
    'functions' => array( 'edit' ),
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array( 'RuleID' )
);

$FunctionList = array();
$FunctionList['read'] = array();
$FunctionList['edit'] = array();
