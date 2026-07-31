<?php


class expLayoutsExpHttpCache
{
    public function getCacheTags( $content )
    {
        $contentObject = expLayoutsSiteAPI::getContentObject( $content );
        if ( !$contentObject instanceof eZContentObject )
            return array();
        return array( 'content-' . $contentObject->attribute( 'id' ) );
    }


    public function invalidateTags( array $tags )
    {
        foreach ( $tags as $tag )
        {
            eZDebug::writeNotice( 'Invalidate cache tag: ' . $tag, 'expLayoutsExpHttpCache' );
        }
        return true;
    }
}
