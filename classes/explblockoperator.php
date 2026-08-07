<?php
/**
 * Template operators for {explblock} extraction.
 *
 *   $html|expl_parse          -> array( 'blocks' => [...], 'stripped' => string )
 *   $html|expl_strip          -> string, markers removed
 *   $parsed|expl_has('name')  -> bool
 *   $array|expl_first()       -> first element or empty string
 */

require_once __DIR__ . '/explblockparser.php';

class ExplBlockOperator
{
    public $Operators = array( 'expl_parse', 'expl_strip', 'expl_has', 'expl_first' );
    public $MaxParam = 2;

    function operatorList()
    {
        return $this->Operators;
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        $def = array_fill( 0, $this->MaxParam, array( "type" => "mixed", "required" => false, "default" => null ) );
        $list = array();
        foreach ( $this->Operators as $op )
        {
            $list[$op] = $def;
        }
        return $list;
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        $arg0 = ( $operatorValue !== null ) ? $operatorValue : ( isset( $namedParameters[0] ) ? $namedParameters[0] : null );
        $arg1 = isset( $namedParameters[1] ) ? $namedParameters[1] : null;

        switch ( $operatorName )
        {
            case 'expl_parse':
                $operatorValue = ExplBlockParser::parse( $arg0 );
                break;

            case 'expl_strip':
                $operatorValue = ExplBlockParser::strip( $arg0 );
                break;

            case 'expl_first':
                if ( is_array( $arg0 ) && !empty( $arg0 ) )
                {
                    $operatorValue = reset( $arg0 );
                }
                else if ( is_string( $arg0 ) )
                {
                    $operatorValue = $arg0;
                }
                else
                {
                    $operatorValue = '';
                }
                break;

            case 'expl_has':
                $blocks = null;
                $name = null;

                if ( is_string( $arg0 ) )
                {
                    $result = ExplBlockParser::parse( $arg0 );
                    $blocks = $result['blocks'];
                    $name = $arg1;
                }
                else if ( is_array( $arg0 ) )
                {
                    if ( isset( $arg0['blocks'] ) )
                    {
                        $blocks = $arg0['blocks'];
                        $name = $arg1;
                    }
                    else
                    {
                        $blocks = $arg0;
                        $name = $arg1;
                    }
                }

                if ( $name !== null && is_array( $blocks ) && isset( $blocks[$name] ) && !empty( $blocks[$name] ) )
                {
                    $operatorValue = true;
                }
                else
                {
                    $operatorValue = false;
                }
                break;
        }
    }
}
