<?php
class expLayoutsMongoInstaller
{
    static function install( $schemaFile )
    {
        if ( !class_exists( 'MongoDB\Client' ) )
            return array( 'success' => false, 'error' => 'MongoDB PHP extension (mongodb) is not available.' );

        $schema = json_decode( file_get_contents( $schemaFile ), true );
        if ( !is_array( $schema ) || !isset( $schema['collections'] ) )
            return array( 'success' => false, 'error' => 'Invalid schema.json' );

        $db = eZDB::instance();
        if ( !method_exists( $db, 'getClient' ) )
            return array( 'success' => false, 'error' => 'Active database driver does not expose MongoDB client.' );

        $client = $db->getClient();
        $databaseName = isset( $schema['database'] ) ? $schema['database'] : $db->DB;
        $database = $client->selectDatabase( $databaseName );

        $created = 0;
        $indexes = 0;
        $errors = array();

        foreach ( $schema['collections'] as $collectionDef )
        {
            $name = $collectionDef['name'];
            try
            {
                $database->createCollection( $name );
                $created++;
            }
            catch ( Exception $e )
            {
                // Collection may already exist; continue
                if ( strpos( $e->getMessage(), 'already exists' ) === false )
                    $errors[] = $name . ': ' . $e->getMessage();
            }

            $collection = $database->selectCollection( $name );
            if ( isset( $collectionDef['indexes'] ) && is_array( $collectionDef['indexes'] ) )
            {
                foreach ( $collectionDef['indexes'] as $indexDef )
                {
                    $keys = isset( $indexDef['keys'] ) ? $indexDef['keys'] : array();
                    $options = isset( $indexDef['options'] ) ? $indexDef['options'] : array();
                    if ( !is_array( $keys ) || count( $keys ) === 0 )
                        continue;
                    try
                    {
                        $collection->createIndex( $keys, $options );
                        $indexes++;
                    }
                    catch ( Exception $e )
                    {
                        $errors[] = $name . ' index: ' . $e->getMessage();
                    }
                }
            }
        }

        return array(
            'success' => count( $errors ) === 0,
            'created' => $created,
            'indexes' => $indexes,
            'error' => count( $errors ) > 0 ? implode( '; ', $errors ) : '',
        );
    }
}
