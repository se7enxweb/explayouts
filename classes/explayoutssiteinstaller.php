<?php

/**
 * eZ Publish 4 port of Netgen Site Installer Bundle.
 */
class expLayoutsSiteInstaller
{
    protected $installerDataPath;
    protected $storagePath;
    protected $additionalSchemaFiles = array();
    protected $output = array();

    public function __construct( $installerDataPath = 'extension/explayouts/data', $storagePath = 'var/ezwebin_site/storage' )
    {
        $this->installerDataPath = $installerDataPath;
        $this->storagePath = $storagePath;
    }

    public function setInstallerDataPath( $installerDataPath )
    {
        $this->installerDataPath = $installerDataPath;
        return $this;
    }

    public function setStoragePath( $storagePath )
    {
        $this->storagePath = $storagePath;
        return $this;
    }

    public function addSchemaFile( $schemaFile, $controlTable )
    {
        $this->additionalSchemaFiles[] = array( $schemaFile, $controlTable );
        return $this;
    }

    public function tableExists( $tableName )
    {
        $db = eZDB::instance();
        // Generic existence check that works for both MySQL and SQLite.
        // A successful SELECT 1 means the table is present; a query failure
        // (false result) means it is missing.
        $result = $db->arrayQuery( "SELECT 1 FROM " . $db->escapeString( $tableName ) . " LIMIT 1" );
        return $result !== false;
    }

    public function tableHasData( $tableName )
    {
        $db = eZDB::instance();
        $result = $db->arrayQuery( "SELECT COUNT(*) AS count FROM `" . $db->escapeString( $tableName ) . "`" );
        return isset( $result[0]['count'] ) && (int)$result[0]['count'] > 0;
    }

    public function runQueriesFromFile( $file )
    {
        if ( !file_exists( $file ) )
        {
            $this->output[] = "File not found: $file";
            return false;
        }

        $db = eZDB::instance();
        $sql = file_get_contents( $file );
        $queries = array_filter( array_map( 'trim', explode( ';', $sql ) ) );
        foreach ( $queries as $query )
        {
            if ( $query === '' )
                continue;
            $db->query( $query );
        }
        $this->output[] = "Imported $file";
        return true;
    }

    public function importSchema( $schemaFile = null, $controlTable = null )
    {
        $schemaFile = $schemaFile !== null ? $schemaFile : $this->installerDataPath . '/schema.sql';
        $controlTable = $controlTable !== null ? $controlTable : 'ezcontentobject';

        if ( $this->tableExists( $controlTable ) )
        {
            $this->output[] = "Schema already exists ($controlTable), skipping $schemaFile";
            return false;
        }

        return $this->runQueriesFromFile( $schemaFile );
    }

    public function importData( $dataFile = null, $controlTable = null )
    {
        $dataFile = $dataFile !== null ? $dataFile : $this->installerDataPath . '/data.sql';
        $controlTable = $controlTable !== null ? $controlTable : 'ezcontentobject';

        if ( $this->tableHasData( $controlTable ) )
        {
            $this->output[] = "Data already exists in $controlTable, skipping $dataFile";
            return false;
        }

        return $this->runQueriesFromFile( $dataFile );
    }

    public function importBinaries( $source = null, $destination = null )
    {
        $source = $source !== null ? $source : $this->installerDataPath . '/storage';
        $destination = $destination !== null ? $destination : $this->storagePath;

        if ( !is_dir( $source ) )
        {
            $this->output[] = "No storage source at $source";
            return false;
        }

        if ( is_dir( $destination ) && count( scandir( $destination ) ) > 2 )
        {
            $this->output[] = "Destination $destination already has files, skipping";
            return false;
        }

        $this->recursiveCopy( $source, $destination );
        $this->output[] = "Copied binaries to $destination";
        return true;
    }

    public function install()
    {
        $this->importSchema();
        foreach ( $this->additionalSchemaFiles as $filePair )
        {
            $this->importSchema( $filePair[0], $filePair[1] );
        }
        $this->importData();
        $this->importBinaries();
        return $this->output;
    }

    public function getOutput()
    {
        return $this->output;
    }

    protected function recursiveCopy( $source, $destination )
    {
        $dir = opendir( $source );
        @mkdir( $destination, 0777, true );
        while ( false !== ( $file = readdir( $dir ) ) )
        {
            if ( $file === '.' || $file === '..' )
                continue;
            $src = $source . '/' . $file;
            $dst = $destination . '/' . $file;
            if ( is_dir( $src ) )
                $this->recursiveCopy( $src, $dst );
            else
                copy( $src, $dst );
        }
        closedir( $dir );
    }
}
