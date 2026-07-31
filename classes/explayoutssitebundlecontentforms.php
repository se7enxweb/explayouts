<?php


class expLayoutsSiteBundleContentForms
{
    public static function getFieldValue( $contentId, $fieldIdentifier )
    {
        $content = expLayoutsSiteAPI::loadContent( (int)$contentId );
        if ( !$content instanceof eZContentObject )
            return null;

        $dataMap = $content->attribute( 'data_map' );
        if ( isset( $dataMap[$fieldIdentifier] ) && is_object( $dataMap[$fieldIdentifier] ) )
            return $dataMap[$fieldIdentifier]->content();

        return null;
    }

    public static function setFieldValue( $contentId, $fieldIdentifier, $value )
    {
        $content = expLayoutsSiteAPI::loadContent( (int)$contentId );
        if ( !$content instanceof eZContentObject )
            return false;

        $dataMap = $content->attribute( 'data_map' );
        if ( isset( $dataMap[$fieldIdentifier] ) && is_object( $dataMap[$fieldIdentifier] ) )
        {
            $dataMap[$fieldIdentifier]->fromString( $value );
            $dataMap[$fieldIdentifier]->store();
            return true;
        }

        return false;
    }


}
