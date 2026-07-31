<?php


class expLayoutsSiteBundleTopic
{
    public static function getTags( $content )
    {
        $content = expLayoutsSiteAPI::getContentObject( $content );
        if ( !$content instanceof eZContentObject )
            return array();

        $dataMap = $content->attribute( 'data_map' );
        if ( isset( $dataMap['tags'] ) && is_object( $dataMap['tags'] ) )
        {
            $tags = $dataMap['tags']->content();
            return is_array( $tags ) ? $tags : array();
        }

        return array();
    }


    public static function getChildren( $topicId )
    {
        return expLayoutsSiteAPI::filterChildren( (int)$topicId, array( 'limit' => 25 ) );
    }
}
