<?php
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

    function rulesForNode( $nodeId )
    {
        $nodeId = (int) $nodeId;
        $rules = array();
        foreach ( expLayoutsRule::fetchEnabled() as $rule )
        {
            foreach ( $rule->targets() as $target )
            {
                if ( $target->attribute( 'target_type' ) === 'node' && (int)$target->attribute( 'target_value' ) == $nodeId )
                {
                    $layout = expLayoutsLayout::fetch( $rule->attribute( 'layout_id' ) );
                    $rules[] = array(
                        'id' => $rule->attribute( 'id' ),
                        'priority' => $rule->attribute( 'priority' ),
                        'enabled' => $rule->attribute( 'enabled' ),
                        'layout_id' => $rule->attribute( 'layout_id' ),
                        'layout_name' => $layout ? $layout->attribute( 'name' ) : '',
                        'layout_identifier' => $layout ? $layout->attribute( 'identifier' ) : '',
                    );
                    break;
                }
            }
        }
        return array( 'result' => $rules );
    }
}
