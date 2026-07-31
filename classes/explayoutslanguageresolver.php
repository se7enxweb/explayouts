<?php

/**
 * Exponential 4 language resolver compatible with the Exp Site API LanguageResolver pattern.
 */
class expLayoutsLanguageResolver
{
    public static function getPrioritizedLanguages()
    {
        $locale = eZLocale::currentLocaleCode();
        return $locale ? array( $locale ) : array( 'eng-GB' );
    }

    public static function getAlwaysAvailable()
    {
        return true;
    }

    public static function resolve( $content )
    {
        $object = null;
        if ( $content instanceof eZContentObject )
            $object = $content;
        elseif ( $content instanceof eZContentObjectTreeNode )
            $object = $content->object();
        elseif ( is_numeric( $content ) )
            $object = eZContentObject::fetch( (int)$content );

        if ( !$object )
            return false;

        $languages = self::getPrioritizedLanguages();
        $available = $object->availableLanguages();

        foreach ( $languages as $language )
        {
            if ( in_array( $language, $available ) )
                return $language;
        }

        return self::getAlwaysAvailable() && $object->attribute( 'language_mask' ) ? $object->attribute( 'initial_language_code' ) : false;
    }

    public static function existsInCurrentLanguage( $content )
    {
        $language = self::resolve( $content );
        if ( !$language )
            return false;

        $current = eZLocale::currentLocaleCode();
        return $language === $current;
    }
}
