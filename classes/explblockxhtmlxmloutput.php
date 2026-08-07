<?php
/**
 * Rich text output handler that escapes the expl: marker sequence.
 *
 * This prevents editors from forging <!--expl:s:...--> markers through
 * any rich text field. The sequence is rewritten to expl&#58; so it is
 * harmless in the final HTML and will not be recognised by ExplBlockParser.
 */

class ExplBlockXHTMLXMLOutput extends eZXHTMLXMLOutput
{
    function &outputText()
    {
        $output = parent::outputText();
        $output = str_replace( 'expl:', 'expl&#58;', $output );
        return $output;
    }
}
