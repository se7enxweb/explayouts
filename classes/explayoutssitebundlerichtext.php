<?php


class expLayoutsSiteBundleRichText
{
    public static function render( $xmlString )
    {
        $xmlString = (string)$xmlString;
        if ( $xmlString === '' )
            return '';

        if ( class_exists( 'eZXHTMLXMLOutput' ) )
        {
            $output = new eZXHTMLXMLOutput( $xmlString, false );
            return $output->outputText();
        }

        return $xmlString;
    }


    public static function textToHtml( $xml )
    {
        return '<div>' . nl2br( strip_tags( $xml ) ) . '</div>';
    }
}
