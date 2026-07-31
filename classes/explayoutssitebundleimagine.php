<?php


class expLayoutsSiteBundleImagine
{
    public static function getImageAlias( $source, $aliasName )
    {
        if ( !is_callable( array( 'eZImageManager', 'createImageAlias' ) ) )
            return $source;

        $manager = eZImageManager::instance();
        $manager->createImageAlias( $source, $aliasName );

        return $source;
    }


    public static function resize( $source, $width, $height )
    {
        $manager = eZImageManager::instance();
        return $manager->createImageAlias( $source, array( 'name' => ' resized', 'width' => (int)$width, 'height' => (int)$height ) );
    }
}
