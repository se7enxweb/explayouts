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

    /**
     * Whether a table is present, asked without provoking an error.
     *
     * This used to run "SELECT 1 FROM <table> LIMIT 1" and read the failure
     * as the answer. No engine agreed with that reading: MongoDB returns no
     * documents for a collection it has never heard of, so every table looked
     * present and the schema step skipped itself; MySQL raises, which left
     * the caller with a fatal error instead of false. Only SQLite behaved as
     * the comment claimed.
     *
     * Each engine is therefore asked through its own catalogue, which answers
     * for a missing table as readily as for a present one.
     */
    public function tableExists( $tableName )
    {
        $db = eZDB::instance();
        $engine = $db->databaseName();

        if ( $engine === 'mongo' )
            return in_array( $tableName, $db->listCollectionNames(), true );

        if ( $engine === 'sqlite' )
        {
            $rows = $db->arrayQuery( "SELECT name FROM sqlite_master WHERE type = 'table' AND name = '"
                . $db->escapeString( $tableName ) . "'" );
            return is_array( $rows ) && count( $rows ) > 0;
        }

        // information_schema is common to MySQL and PostgreSQL; only the
        // function naming the current schema differs.
        $currentSchema = $engine === 'postgresql' ? 'current_schema()' : 'DATABASE()';
        $rows = $db->arrayQuery( 'SELECT table_name FROM information_schema.tables'
            . ' WHERE table_schema = ' . $currentSchema
            . " AND table_name = '" . $db->escapeString( $tableName ) . "'" );

        return is_array( $rows ) && count( $rows ) > 0;
    }

    public function tableHasData( $tableName )
    {
        // Counting a table that is not there raises on MySQL, so existence is
        // settled first. A table that does not exist holds no data.
        if ( !$this->tableExists( $tableName ) )
            return false;

        $db = eZDB::instance();
        // The table name was quoted in MySQL backticks, which the MongoDB
        // driver does not strip: the count never parsed, every table looked
        // empty, and the data step would have re-imported over populated
        // tables. Unquoted, the statement is accepted by all three engines.
        $result = $db->arrayQuery( "SELECT COUNT(*) AS count FROM " . $db->escapeString( $tableName ) );
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
