<?php


class expLayoutsSiteBundleCommand
{
    public static function run( $scriptName, array $params = array() )
    {
        $cli = eZCLI::instance();
        $cli->output( 'Running command: ' . $scriptName );
        return true;
    }


    public static function runCommand( $scriptName, array $params = array() )
    {
        $cli = eZCLI::instance();
        $cli->output( 'Running command: ' . $scriptName );
        return true;
    }
}
