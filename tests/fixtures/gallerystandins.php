<?php
/**
 * Stand-ins of the kernel and of the collection classes for GalleryBlocksTest, loaded in the test's own process
 * (RunTestsInSeparateProcesses), so nothing here meets the real classes or another test's stand-ins. Each is declared
 * only if the class is not loaded yet, checked without autoloading.
 *
 * @license GPL-2.0-or-later
 */

/** An image alias handler: aliases by name, each array( 'url' => ... ). */
class gbImage
{
    public $aliases;
    public function __construct( array $aliases ) { $this->aliases = $aliases; }
    public function hasAttribute( $name ) { return isset( $this->aliases[$name] ); }
    public function attribute( $name ) { return isset( $this->aliases[$name] ) ? $this->aliases[$name] : null; }
}

class gbAttribute
{
    public $content;
    public function __construct( $content ) { $this->content = $content; }
    public function hasAttribute( $name ) { return $name === 'content'; }
    public function attribute( $name ) { return $name === 'content' ? $this->content : null; }
}

class gbObject
{
    public $dataMap;
    public function __construct( array $dataMap ) { $this->dataMap = $dataMap; }
    public function attribute( $name ) { return $name === 'data_map' ? $this->dataMap : null; }
}

if ( !class_exists( 'eZContentObjectTreeNode', false ) )
{
    class eZContentObjectTreeNode
    {
        public static $nodes = array();
        public static $children = array();
        public static $showInvisible = false;
        public $attributes;
        public function __construct( array $attributes ) { $this->attributes = $attributes; }
        public function attribute( $name ) { return isset( $this->attributes[$name] ) ? $this->attributes[$name] : null; }
        public function canRead() { return !isset( $this->attributes['can_read'] ) || $this->attributes['can_read']; }
        public static function showInvisibleNodes() { return self::$showInvisible; }
        public static function fetch( $nodeId ) { return isset( self::$nodes[$nodeId] ) ? self::$nodes[$nodeId] : null; }
        public static function subTreeByNodeID( $params, $parentNodeId )
        {
            return isset( self::$children[$parentNodeId] ) ? self::$children[$parentNodeId] : array();
        }
    }
}

if ( !class_exists( 'expLayoutsCollection', false ) )
{
    class expLayoutsCollection
    {
        public static $byBlock = array();
        public static $fetches = 0;
        public $attributes;
        public function __construct( array $attributes ) { $this->attributes = $attributes; }
        public function attribute( $name ) { return isset( $this->attributes[$name] ) ? $this->attributes[$name] : null; }
        public static function fetchByBlock( $blockId )
        {
            self::$fetches++;
            return isset( self::$byBlock[$blockId] ) ? self::$byBlock[$blockId] : null;
        }
    }
}

if ( !class_exists( 'expLayoutsCollectionItem', false ) )
{
    class expLayoutsCollectionItem
    {
        public static $byCollection = array();
        public static $fetches = 0;
        public $attributes;
        public function __construct( array $attributes ) { $this->attributes = $attributes; }
        public function attribute( $name ) { return isset( $this->attributes[$name] ) ? $this->attributes[$name] : null; }
        public static function fetchByCollection( $collectionId, $asObject = true )
        {
            self::$fetches++;
            return isset( self::$byCollection[$collectionId] ) ? self::$byCollection[$collectionId] : array();
        }
    }
}

if ( !class_exists( 'expLayoutsDynamicCollection', false ) )
{
    class expLayoutsDynamicCollection
    {
        public static function fetch( $collection ) { return false; }
    }
}

/** The manual collection's offset/limit row (expLayoutsManualQueryHandler); no slicing. */
if ( !class_exists( 'eZDB', false ) )
{
    class eZDB
    {
        public static function instance() { return new self(); }
        public function arrayQuery( $sql ) { return array(); }
    }
}

/** The query handlers: the real manual one, 'children' returns the children of node_id. */
class gbChildrenQueryHandler
{
    public function fetch( $parameters )
    {
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( array(), (int)$parameters['node_id'] );
        return array( 'total' => count( $nodes ), 'items' => $nodes );
    }
}

if ( !class_exists( 'expLayoutsQueryHandlerFactory', false ) )
{
    class expLayoutsQueryHandlerFactory
    {
        public static $asked = array();
        public static function get( $type )
        {
            self::$asked[] = $type;
            if ( $type === 'manual' )
                return new expLayoutsManualQueryHandler();
            return $type === 'children' ? new gbChildrenQueryHandler() : false;
        }
    }
}

if ( !class_exists( 'ezpHttpCacheListener', false ) )
{
    class ezpHttpCacheListener
    {
        public static $tags = array();
        public static function addTags( array $tags ) { self::$tags = array_merge( self::$tags, $tags ); }
    }
}

/**
 * eZURI::transformURI() as ezroot('no') calls it: the web root (eZSys::wwwDir(), '' for an installation at the server
 * root, /sub/path when a test sets it) in front of a path, an address with a scheme left alone.
 */
if ( !class_exists( 'eZSys', false ) )
{
    class eZSys
    {
        public static $wwwDir = '';
        public static function wwwDir() { return self::$wwwDir; }
    }
}
if ( !class_exists( 'eZURI', false ) )
{
    class eZURI
    {
        public static function transformURI( &$href, $ignoreIndexDir = false, $serverURL = null, $htmlEscape = true )
        {
            if ( preg_match( '#^[a-zA-Z0-9]+:#', $href ) || substr( $href, 0, 2 ) === '//' )
                return false;
            $href = eZSys::wwwDir() . '/' . ltrim( $href, '/' );
            return true;
        }
    }
}

$classes = dirname( __DIR__, 2 ) . '/classes/';
require_once $classes . 'explayoutssafevalue.php';
require_once $classes . 'explayoutsblockhandlerinterface.php';
require_once $classes . 'explayoutsqueryhandlerinterface.php';
require_once $classes . 'explayoutsmanualqueryhandler.php';
require_once $classes . 'explayoutsgalleryblockhandler.php';
require_once $classes . 'explayoutsabstractcontentblockhandler.php';
require_once $classes . 'explayoutscarouselblockhandler.php';
