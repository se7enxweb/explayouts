<?php
/**
 * Template function {explblock name=... [if=...] [wrap=...]} ... {/explblock}
 *
 * Emits the wrapped content plus HTML comment markers so that a later
 * consumer can extract the region from the rendered string.
 */

class ExplBlockFunction
{
    function functionList()
    {
        return array( 'explblock' );
    }

    function functionTemplateHints()
    {
        return array(
            'explblock' => array(
                'parameters' => true,
                'static' => false,
                'transform-children' => true,
                'tree-transformation' => false,
                'transform-parameters' => true,
            )
        );
    }

    function attributeList()
    {
        return array( 'explblock' => true );
    }

    function hasChildren()
    {
        return true;
    }

    function process( $tpl, &$textElements, $functionName, $functionChildren, $functionParameters, $functionPlacement, $rootNamespace, $currentNamespace )
    {
        if ( !isset( $functionParameters['name'] ) )
        {
            $tpl->missingParameter( 'explblock', 'name' );
            return;
        }

        $name = $tpl->elementValue( $functionParameters['name'], $rootNamespace, $currentNamespace, $functionPlacement );

        $if = true;
        if ( isset( $functionParameters['if'] ) )
            $if = $tpl->elementValue( $functionParameters['if'], $rootNamespace, $currentNamespace, $functionPlacement );

        $wrap = true;
        if ( isset( $functionParameters['wrap'] ) )
            $wrap = $tpl->elementValue( $functionParameters['wrap'], $rootNamespace, $currentNamespace, $functionPlacement );

        if ( eZTemplate::isDebugEnabled() )
        {
            if ( !preg_match( '/^[a-z][a-z0-9_]{0,63}$/', $name ) )
            {
                $tpl->error( 'explblock', "Block name '" . $name . "' does not match the required grammar.", $functionPlacement );
                return;
            }
        }

        $childTextElements = array();
        if ( is_array( $functionChildren ) )
        {
            foreach ( array_keys( $functionChildren ) as $childKey )
            {
                $child = $functionChildren[$childKey];
                $tpl->processNode( $child, $childTextElements, $rootNamespace, $currentNamespace );
            }
        }

        $content = implode( '', $childTextElements );

        if ( $if && $wrap )
        {
            $textElements[] = '<!--expl:s:' . $name . '-->' . $content . '<!--expl:e:' . $name . '-->';
        }
        else
        {
            $textElements[] = $content;
        }
    }
}
