<?php


class expLayoutsExpLocale
{
    public function getCurrentLocale()
    {
        $locale = eZLocale::currentLocaleCode();
        return $locale !== '' ? $locale : 'eng-GB';
    }


    public static function getLocales()
    {
        $ini = eZINI::instance( 'site.ini' );
        $locale = $ini->hasVariable( 'RegionalSettings', 'Locale' ) ? $ini->variable( 'RegionalSettings', 'Locale' ) : 'eng-GB';
        return array( $locale );
    }

    public static function getLanguageName( $locale )
    {
        return $locale;
    }
}
