<?php
/**
 * response/output event listener that renders an expl block debug overlay
 * when the page is requested with ?expl_debug=1.
 */

require_once 'extension/explayouts/classes/explblockparser.php';

class ExplBlockDebug
{
    public static function output( $output )
    {
        $http = eZHTTPTool::instance();
        if ( $http->getVariable( 'expl_debug' ) != '1' )
        {
            return $output;
        }

        $parsed = ExplBlockParser::parse( $output );
        $blocks = $parsed['blocks'];

        $html = '<div id="explDebugOverlay" style="position:fixed;bottom:0;left:0;right:0;max-height:300px;overflow:auto;background:#fff;color:#000;border-top:2px solid #000;padding:1rem;font-family:monospace;font-size:12px;z-index:100000;">';
        $html .= '<h3 style="margin:0 0 1rem 0;">expl blocks</h3>';

        if ( empty( $blocks ) )
        {
            $html .= '<p>No expl blocks found.</p>';
        }
        else
        {
            $html .= '<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse;">';
            $html .= '<tr><th align="left">name</th><th align="left">occurrences</th><th align="left">total bytes</th></tr>';

            foreach ( $blocks as $name => $list )
            {
                $count = count( $list );
                $size = 0;
                foreach ( $list as $content )
                {
                    $size += strlen( $content );
                }
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars( $name, ENT_QUOTES ) . '</td>';
                $html .= '<td>' . (int)$count . '</td>';
                $html .= '<td>' . (int)$size . '</td>';
                $html .= '</tr>';
            }

            $html .= '</table>';
        }

        $html .= '</div>';

        if ( strpos( $output, '</body>' ) !== false )
        {
            $output = str_replace( '</body>', $html . '</body>', $output );
        }
        else
        {
            $output .= $html;
        }

        return $output;
    }
}
