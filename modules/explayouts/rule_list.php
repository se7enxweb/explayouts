<?php
$http = eZHTTPTool::instance();
$module = $Params['Module'];

if ( !eZUser::currentUser()->hasAccessTo( 'explayouts', 'read' ) )
{
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$message = '';
$error = '';

if ( eZUser::currentUser()->hasAccessTo( 'explayouts', 'edit' ) && $http->hasPostVariable( 'DeleteRule' ) )
{
    $deleteId = (int)$http->postVariable( 'DeleteRuleID' );
    $rule = expLayoutsRule::fetch( $deleteId );
    if ( $rule )
    {
        foreach ( $rule->targets() as $t ) $t->remove();
        foreach ( $rule->conditions() as $c ) $c->remove();
        $rule->remove();
        $message = 'Rule deleted.';
    }
    else
    {
        $error = 'Rule not found.';
    }
}

if ( eZUser::currentUser()->hasAccessTo( 'explayouts', 'edit' ) && $http->hasPostVariable( 'CopyRule' ) )
{
    $copyId = (int)$http->postVariable( 'CopyRuleID' );
    $rule = expLayoutsRule::fetch( $copyId );
    if ( $rule )
    {
        $newRule = expLayoutsRule::create( (int)$rule->attribute( 'layout_id' ) );
        $newRule->setAttribute( 'priority', (int)$rule->attribute( 'priority' ) );
        $newRule->setAttribute( 'enabled', (int)$rule->attribute( 'enabled' ) );
        $newRule->store();

        foreach ( $rule->targets() as $target )
        {
            $newTarget = expLayoutsRuleTarget::create( $newRule->attribute( 'id' ), $target->attribute( 'target_type' ), $target->attribute( 'target_value' ) );
            $newTarget->store();
        }

        foreach ( $rule->conditions() as $condition )
        {
            $newCondition = expLayoutsRuleCondition::create( $newRule->attribute( 'id' ), $condition->attribute( 'condition_type' ), $condition->attribute( 'condition_value' ) );
            $newCondition->store();
        }

        $message = 'Rule copied.';
    }
    else
    {
        $error = 'Rule not found.';
    }
}

$rules = expLayoutsRule::fetchEnabled();

$tpl = eZTemplate::factory();
$tpl->setVariable( 'rules', $rules );
$tpl->setVariable( 'message', $message );
$tpl->setVariable( 'error', $error );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:explayouts/rule_list.tpl' );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'explayouts/rule', 'Layout Rules' ) ) );
return $Result;
