<?php

/**
 * Exponential 4 Site registry compatible with the Exp Site API Site interface.
 *
 * Provides access to Load, Filter, Find, Relation and URL services through a
 * single entry point.
 */
class expLayoutsSite
{
    protected static $instance = null;

    /**
     * Singleton accessor.
     */
    public static function getInstance()
    {
        if ( self::$instance === null )
            self::$instance = new self();

        return self::$instance;
    }

    public function getSettings()
    {
        return expLayoutsSiteAPI::getSettings();
    }

    public function getFilterService()
    {
        return new expLayoutsSiteAPI();
    }

    public function getFindService()
    {
        return new expLayoutsSiteAPI();
    }

    public function getLoadService()
    {
        return new expLayoutsSiteAPI();
    }

    public function getRelationService()
    {
        return new expLayoutsSiteAPI();
    }

    public function getUrlGenerator()
    {
        return new expLayoutsSiteAPI();
    }
}
