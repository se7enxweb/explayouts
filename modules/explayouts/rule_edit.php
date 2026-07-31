<?php
$http = eZHTTPTool::instance();
$module = $Params['Module'];

if ( !eZUser::currentUser()->hasAccessTo( 'explayouts', 'edit' ) )
{
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$ruleId = isset( $Params['RuleID'] ) ? (int)$Params['RuleID'] : 0;
if ( $ruleId > 0 )
    $rule = expLayoutsRule::fetch( $ruleId );
else
    $rule = expLayoutsRule::create( 0, 0 );

if ( !$rule )
    return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );

$message = '';
$error = '';

if ( $http->hasPostVariable( 'SaveRule' ) )
{
    $layoutId = (int)$http->postVariable( 'LayoutID' );
    $priority = (int)$http->postVariable( 'Priority' );
    $enabled = $http->hasPostVariable( 'Enabled' ) ? 1 : 0;

    $rule->setAttribute( 'layout_id', $layoutId );
    $rule->setAttribute( 'priority', $priority );
    $rule->setAttribute( 'enabled', $enabled );
    $rule->store();

    // remove old targets/conditions and re-create
    foreach ( $rule->targets() as $t ) { $t->remove(); }
    foreach ( $rule->conditions() as $c ) { $c->remove(); }

    $targetTypes = $http->hasPostVariable( 'TargetType' ) ? $http->postVariable( 'TargetType' ) : array();
    $targetValues = $http->hasPostVariable( 'TargetValue' ) ? $http->postVariable( 'TargetValue' ) : array();
    for ( $i = 0; $i < count( $targetTypes ); $i++ )
    {
        $type = trim( $targetTypes[$i] );
        $value = trim( $targetValues[$i] );
        if ( $type === '' ) continue;
        $t = new expLayoutsRuleTarget( array(
            'rule_id' => $rule->attribute( 'id' ),
            'target_type' => $type,
            'target_value' => $value,
        ) );
        $t->store();
    }

    $conditionTypes = $http->hasPostVariable( 'ConditionType' ) ? $http->postVariable( 'ConditionType' ) : array();
    $conditionValues = $http->hasPostVariable( 'ConditionValue' ) ? $http->postVariable( 'ConditionValue' ) : array();
    for ( $i = 0; $i < count( $conditionTypes ); $i++ )
    {
        $type = trim( $conditionTypes[$i] );
        $value = trim( $conditionValues[$i] );
        if ( $type === '' ) continue;
        $c = new expLayoutsRuleCondition( array(
            'rule_id' => $rule->attribute( 'id' ),
            'condition_type' => $type,
            'condition_value' => $value,
        ) );
        $c->store();
    }

    $message = 'Rule saved.';
}

$layouts = expLayoutsLayout::fetchList( 2 );
$targets = $rule->targets();
$conditions = $rule->conditions();

$tpl = eZTemplate::factory();
$tpl->setVariable( 'rule', $rule );
$tpl->setVariable( 'layouts', $layouts );
$tpl->setVariable( 'targets', $targets );
$tpl->setVariable( 'conditions', $conditions );
$tpl->setVariable( 'message', $message );
$tpl->setVariable( 'error', $error );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:explayouts/rule_edit.tpl' );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'explayouts/rule', 'Edit Rule' ) ) );
return $Result;
