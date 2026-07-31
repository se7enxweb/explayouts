<?php


class expLayoutsSiteBundleOpenGraph
{
    public static function getTags( $content )
    {
        $content = expLayoutsSiteAPI::getContentObject( $content );
        if ( !$content instanceof eZContentObject )
            return array();

        $dataMap = $content->attribute( 'data_map' );
        $tags = array();
        $tags['og:title'] = $content->attribute( 'name' );

        if ( isset( $dataMap['image'] ) && is_object( $dataMap['image'] ) )
        {
            $imageContent = $dataMap['image']->content();
            if ( is_object( $imageContent ) && method_exists( $imageContent, 'attribute' ) )
            {
                $url = $imageContent->attribute( 'url_alias' );
                if ( $url !== '' )
                    $tags['og:image'] = $url;
            }
        }

        if ( isset( $dataMap['intro'] ) && is_object( $dataMap['intro'] ) )
            $tags['og:description'] = strip_tags( $dataMap['intro']->content() );

        return $tags;
    }


}
