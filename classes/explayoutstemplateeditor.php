<?php
class expLayoutsTemplateEditor
{
    public static function listTemplates( $root = 'design' )
    {
        $list = array();
        $base = eZSys::rootDir();
        $allowedRoots = self::allowedRoots();

        foreach ( $allowedRoots as $allowedRoot )
        {
            $full = $base . '/' . $allowedRoot;
            if ( !is_dir( $full ) )
                continue;
            self::scan( $full, $allowedRoot, $list );
        }
        sort( $list );
        return $list;
    }

    protected static function allowedRoots()
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        $roots = $ini->variable( 'TemplateEditorSettings', 'AllowedTemplateRoots' );
        if ( !is_array( $roots ) || empty( $roots ) )
            $roots = array( 'design', 'extension' );
        return $roots;
    }

    protected static function scan( $dir, $relative, &$list )
    {
        $d = @dir( $dir );
        if ( !$d )
            return;
        while ( ( $entry = $d->read() ) !== false )
        {
            if ( $entry === '.' || $entry === '..' )
                continue;
            $full = $dir . '/' . $entry;
            $rel  = $relative . '/' . $entry;
            if ( is_dir( $full ) )
            {
                self::scan( $full, $rel, $list );
            }
            else if ( is_file( $full ) && substr( $entry, -4 ) === '.tpl' )
            {
                $list[] = $rel;
            }
        }
        $d->close();
    }

    public static function read( $path )
    {
        $file = self::resolve( $path );
        if ( $file === false || !is_readable( $file ) )
            return false;
        return file_get_contents( $file );
    }

    public static function save( $path, $content )
    {
        $file = self::resolve( $path );
        if ( $file === false || !is_writable( dirname( $file ) ) )
            return false;

        if ( is_file( $file ) )
        {
            copy( $file, $file . '.bak' );
        }

        return file_put_contents( $file, $content ) !== false;
    }

    protected static function resolve( $path )
    {
        $base = realpath( eZSys::rootDir() );
        $allowedRoots = self::allowedRoots();
        $cleanPath = ltrim( $path, '/' );
        $candidate = $base . '/' . $cleanPath;
        $dir = @realpath( dirname( $candidate ) );

        if ( $dir === false )
            return false;

        foreach ( $allowedRoots as $allowedRoot )
        {
            $rootReal = @realpath( $base . '/' . $allowedRoot );
            if ( $rootReal !== false && strpos( $dir, $rootReal ) === 0 )
            {
                return $candidate;
            }
        }
        return false;
    }
}
