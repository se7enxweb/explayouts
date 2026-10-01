<?php

if ( !class_exists( 'expLayoutsFunctionCollection', false ) ) {
class expLayoutsFunctionCollection
{
    function fetchLayout( $identifier )
    {
        $layout = expLayoutsLayout::fetchByIdentifier( $identifier, 2 );
        if ( !$layout )
            return array( 'result' => false );

        return array( 'result' => expLayoutsRenderer::prepareLayout( $layout, 2 ) );
    }

    function resolveLayout( $path = false )
    {
        if ( $path === false )
            $path = eZSys::requestURI();

        $layout = expLayoutsResolver::resolve( $path );
        if ( !$layout )
            return array( 'result' => false );

        return array( 'result' => expLayoutsRenderer::prepareLayout( $layout, 2 ) );
    }

    function resolveLayoutForNode( $nodeId )
    {
        $nodeId = (int)$nodeId;
        $node = eZContentObjectTreeNode::fetch( $nodeId );
        if ( $node )
        {
            $layout = expLayoutsResolver::resolve( $node->attribute( 'url_alias' ) );
            if ( $layout )
                return array( 'result' => expLayoutsRenderer::prepareLayout( $layout, 2 ) );
        }
        return array( 'result' => false );
    }

    /**
     * The layout that applies to the node, summarised (expLayoutsRenderer::summarizeLayout()):
     * enough to name it and count its zones and blocks, for the admin node view.
     */
    function layoutSummaryForNode( $nodeId )
    {
        $node = eZContentObjectTreeNode::fetch( (int)$nodeId );
        if ( $node )
        {
            $layout = expLayoutsResolver::resolve( $node->attribute( 'url_alias' ) );
            if ( $layout )
                return array( 'result' => expLayoutsRenderer::summarizeLayout( $layout, 2 ) );
        }
        return array( 'result' => false );
    }

    function rulesForNode( $nodeId )
    {
        $nodeId = (int) $nodeId;
        // The rules that target this node, read from its targets: one query,
        // instead of the targets of every enabled rule.
        $ruleIds = array();
        foreach ( expLayoutsRuleTarget::fetchByTarget( 'node', (string)$nodeId ) as $target )
            $ruleIds[(int)$target->attribute( 'rule_id' )] = true;
        $rules = array();
        if ( !$ruleIds )
            return array( 'result' => $rules );
        foreach ( expLayoutsRule::fetchEnabled() as $rule )
        {
            if ( !isset( $ruleIds[(int)$rule->attribute( 'id' )] ) )
                continue;
            $layout = expLayoutsLayout::fetch( $rule->attribute( 'layout_id' ) );
            $rules[] = array(
                'id' => $rule->attribute( 'id' ),
                'priority' => $rule->attribute( 'priority' ),
                'enabled' => $rule->attribute( 'enabled' ),
                'layout_id' => $rule->attribute( 'layout_id' ),
                'layout_name' => $layout ? $layout->attribute( 'name' ) : '',
                'layout_identifier' => $layout ? $layout->attribute( 'identifier' ) : '',
            );
        }
        return array( 'result' => $rules );
    }
}
}

