<?php
class expLayoutsQueryHandlerFactory
{
    static function get( $queryType )
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        $section = 'QueryType_' . $queryType;
        if ( !$ini->hasGroup( $section ) )
            return false;

        $handlerClass = $ini->variable( $section, 'Handler' );
        if ( !class_exists( $handlerClass ) )
            return false;

        return new $handlerClass();
    }

    static function getAvailableQueries()
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        if ( !$ini->hasVariable( 'QuerySettings', 'AvailableQueries' ) )
            return array();
        return $ini->variable( 'QuerySettings', 'AvailableQueries' );
    }
}
