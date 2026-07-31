<?php
class expLayoutsInstall
{
    static function runSqlFile( $sqlFile )
    {
        $db = eZDB::instance();
        $path = eZSys::rootDir() . '/' . ltrim( $sqlFile, '/' );

        if ( !file_exists( $path ) )
            return array( 'error' => 'SQL file not found: ' . $sqlFile );

        $sql = file_get_contents( $path );
        $queries = array_filter( array_map( 'trim', preg_split( '/;[\s]*$/m', $sql ) ) );

        $executed = 0;
        $failed = 0;
        $lastError = '';

        foreach ( $queries as $query )
        {
            if ( $query === '' )
                continue;

            $result = $db->query( $query );
            if ( $result === false )
            {
                $failed++;
                $lastError = $db->error() ? $db->error() : 'Unknown error';
            }
            else
            {
                $executed++;
            }
        }

        if ( $failed > 0 )
            return array( 'error' => 'Executed ' . $executed . ' queries, ' . $failed . ' failed. Last error: ' . $lastError );

        return array( 'message' => 'Executed ' . $executed . ' SQL queries.' );
    }
}
