<?php


class expLayoutsSiteBundleRelation
{
    public static function getFieldRelations( $content, $fieldIdentifier, $limit = null )
    {
        return expLayoutsSiteAPI::loadFieldRelations( $content, $fieldIdentifier, array(), $limit );
    }

    public static function getReverseFieldRelations( $content, $fieldIdentifier, $limit = null )
    {
        return expLayoutsSiteAPI::loadReverseFieldRelations( $content, $fieldIdentifier, array(), $limit );
    }


    public static function addFieldRelation( $fromContentId, $toContentId, $fieldIdentifier )
    {
        $from = expLayoutsSiteAPI::loadContent( (int)$fromContentId );
        if ( !$from instanceof eZContentObject )
            return false;
        return true;
    }
}
