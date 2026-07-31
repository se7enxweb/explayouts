<?php


class expLayoutsSiteBundleDataCollector
{
    public static function collect()
    {
        return array(
            'memory' => memory_get_peak_usage( true ),
            'memory_human' => memory_get_peak_usage( true ) / 1024 / 1024,
        );
    }


    public static function collectTiming( $name )
    {
        return array( 'name' => $name, 'time' => microtime( true ) );
    }
}
