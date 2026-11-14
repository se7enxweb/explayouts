<?php
/**
 * The values of a block that go into markup where escaping is not enough: a link or an embed address (|wash keeps
 * "javascript:" working), a colour inside a style attribute, an element name, a template name. Each method returns
 * the value when it is of an allowed form, else a safe replacement ('' or the given fallback).
 *
 * The block handlers normalise their values with it, so every design's templates get safe values; the standard
 * templates check again with the template operators of this class:
 *
 *   {$url|explayouts_safe_url}          a link: http(s), mailto, tel, a relative address, #anchor, ?query
 *   {$url|explayouts_safe_embed_url}    an iframe address: http(s), protocol relative or a path on this site
 *   {$color|explayouts_safe_color}      a CSS colour, else ''
 *   {$tag|explayouts_safe_tag( 2 )}     h1-h6, p or div, else h<level> (h2 for a level outside 1-6)
 *   {$name|explayouts_safe_template_name}  letters, digits and _, else ''
 *   {$class|explayouts_safe_css_class}  class names of letters, digits, _ and -, separated by single spaces
 *
 * A checked value is still printed escaped (|wash, or |ezurl in double quotes for a link): the check decides what a
 * value may be, the escaping how it is written into the markup.
 *
 * @license GPL-2.0-or-later
 */
class expLayoutsSafeValue
{
    /** Schemes a link may use */
    const LINK_SCHEMES = array( 'http', 'https', 'mailto', 'tel' );

    /** Schemes an embedded frame may use */
    const EMBED_SCHEMES = array( 'http', 'https' );

    /** The elements a title block may be */
    const HEADING_TAGS = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div' );

    /**
     * A link address, or '' when it uses a scheme other than http, https, mailto and tel (javascript:, data:,
     * vbscript:, ...). Relative addresses, #anchors and ?queries are kept. HTML entities are decoded before the scheme
     * is read ("&#106;avascript:" is javascript: once a browser has parsed an attribute printed unescaped); the value
     * returned is the value given, and must still be escaped when it is printed.
     *
     * @param mixed $value
     * @return string
     */
    public static function url( $value )
    {
        return self::checkedUrl( $value, self::LINK_SCHEMES, true );
    }

    /**
     * An address for an iframe: http(s), protocol relative (//host/...) or an absolute path on this site; '' for
     * anything else, also #anchors and relative paths.
     *
     * @param mixed $value
     * @return string
     */
    public static function embedUrl( $value )
    {
        return self::checkedUrl( $value, self::EMBED_SCHEMES, false );
    }

    /**
     * The address of a frame of another host (the video and map blocks): an http(s) address, a protocol relative one
     * as https; '' for anything else, also a path on this site (a page of the site in a frame with scripts and its own
     * origin would be outside every sandbox).
     *
     * @param mixed $value
     * @return string
     */
    public static function externalEmbedUrl( $value )
    {
        $url = self::embedUrl( $value );
        if ( $url === '' )
            return '';
        $url = trim( $url );
        if ( substr( $url, 0, 2 ) === '//' )
            return 'https:' . $url;
        return preg_match( '#^https?://#i', $url ) ? $url : '';
    }

    /** The host of an address (without user, port and path), lower case; '' when it has none */
    public static function host( $url )
    {
        $host = is_scalar( $url ) ? parse_url( (string)$url, PHP_URL_HOST ) : null;
        return is_string( $host ) ? strtolower( $host ) : '';
    }

    protected static function checkedUrl( $value, array $schemes, $allowRelative )
    {
        if ( !is_scalar( $value ) )
            return '';
        $value = trim( (string)$value );
        if ( $value === '' )
            return '';

        // Browsers ignore control characters and white space inside a scheme ("java\tscript:"), so the scheme is
        // read from the value without them, and with its entities decoded (twice, for "&amp;#106;").
        $probe = html_entity_decode( html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        $probe = preg_replace( '/[\x00-\x20\x7F]+/', '', $probe );
        if ( preg_match( '#^([a-zA-Z][a-zA-Z0-9+.\-]*):#', $probe, $matches ) )
            return in_array( strtolower( $matches[1] ), $schemes, true ) ? $value : '';

        // No scheme: a backslash at the start is read as a slash by browsers ("\\host" is "//host").
        if ( $probe !== '' && $probe[0] === '\\' )
            return '';
        // A colon before the first slash, ? or # that did not make a scheme above ("1javascript:" is not one, but
        // ":x" or a scheme with characters PHP does not match) is not a relative address either.
        $head = strcspn( $probe, '/?#' );
        if ( strpos( substr( $probe, 0, $head ), ':' ) !== false )
            return '';

        if ( $allowRelative )
            return $value;
        // embeds: a path on this site or a protocol relative address
        return ( $probe !== '' && $probe[0] === '/' ) ? $value : '';
    }

    /**
     * A CSS colour: #rgb, #rgba, #rrggbb, #rrggbbaa, rgb()/rgba()/hsl()/hsla() with numbers, units and angles,
     * var(--name), or a colour name; $fallback for anything else (so nothing but a colour reaches a style attribute:
     * no url(, no ;, no quotes).
     *
     * @param mixed $value
     * @param string $fallback
     * @return string
     */
    public static function color( $value, $fallback = '' )
    {
        if ( !is_scalar( $value ) )
            return $fallback;
        $value = trim( (string)$value );
        if ( preg_match( '/^#(?:[0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $value )
             || preg_match( '/^(?:rgb|rgba|hsl|hsla)\(\s*[0-9.%\s,\/+\-]*(?:deg|rad|grad|turn)?[0-9.%\s,\/+\-]*\)$/i', $value )
             || preg_match( '/^var\(\s*--[A-Za-z0-9_\-]+\s*\)$/', $value )
             || preg_match( '/^[a-zA-Z]{3,30}$/', $value ) )
            return $value;
        return $fallback;
    }

    /**
     * The element of a title: $tag when it is h1-h6, p or div; else h<$level>, and h2 for a level outside 1-6.
     *
     * @param mixed $tag
     * @param mixed $level
     * @return string
     */
    public static function headingTag( $tag, $level = 2 )
    {
        $tag = is_scalar( $tag ) ? strtolower( trim( (string)$tag ) ) : '';
        if ( in_array( $tag, self::HEADING_TAGS, true ) )
            return $tag;
        $level = is_numeric( $level ) ? (int)$level : 2;
        return ( $level >= 1 && $level <= 6 ) ? 'h' . $level : 'h2';
    }

    /**
     * A template name of the template block (design:explayouts/tpl_block/<name>.tpl): lower case letters, digits
     * and _ only, else ''.
     *
     * @param mixed $name
     * @return string
     */
    public static function templateName( $name )
    {
        $name = is_scalar( $name ) ? trim( (string)$name ) : '';
        return preg_match( '/^[a-z0-9_]+$/', $name ) ? $name : '';
    }

    /**
     * Class names for a class attribute: the tokens of letters, digits, _ and - only, separated by single spaces;
     * anything else is dropped (a quote or > closed the attribute).
     *
     * @param mixed $value
     * @return string
     */
    public static function cssClass( $value )
    {
        if ( !is_scalar( $value ) )
            return '';
        $tokens = array();
        foreach ( preg_split( '/\s+/', trim( (string)$value ) ) as $token )
        {
            if ( preg_match( '/^[A-Za-z0-9_\-]+$/', $token ) )
                $tokens[] = $token;
        }
        return implode( ' ', $tokens );
    }

    /**
     * A file below the installation's web root (an image alias, stored as var/<site>/storage/... without a leading
     * slash) as the browser has to ask for it, as the template operator ezroot('no') writes it: the web root in front,
     * so an installation served from a sub path (/sub/path/var/...) finds it, or the address of the cluster's file
     * server where one is configured. An address with a scheme (http:, https:) or a protocol relative one stays as it
     * is; '' stays ''. Without the kernel (tests) the path gets a leading slash, as before.
     *
     * @param mixed $path
     * @return string
     */
    public static function webRootUrl( $path )
    {
        $path = is_scalar( $path ) ? (string)$path : '';
        if ( $path === '' || preg_match( '#^([a-z][a-z0-9+.\-]*:|//)#i', $path ) )
            return $path;

        if ( class_exists( 'eZURI' ) && class_exists( 'eZSys' ) )
        {
            eZURI::transformURI( $path, true, 'relative', false );
            return $path;
        }

        return '/' . ltrim( $path, '/' );
    }

    // ------------------------------------------------------------------
    // Template operators
    // ------------------------------------------------------------------

    public function operatorList()
    {
        return array( 'explayouts_safe_url', 'explayouts_safe_embed_url', 'explayouts_safe_color', 'explayouts_safe_tag',
                      'explayouts_safe_template_name', 'explayouts_safe_css_class' );
    }

    public function namedParameterPerOperator()
    {
        return true;
    }

    public function namedParameterList()
    {
        return array(
            'explayouts_safe_url' => array(),
            'explayouts_safe_embed_url' => array(),
            'explayouts_safe_template_name' => array(),
            'explayouts_safe_css_class' => array(),
            'explayouts_safe_color' => array( 'fallback' => array( 'type' => 'string', 'required' => false, 'default' => '' ) ),
            'explayouts_safe_tag' => array( 'level' => array( 'type' => 'integer', 'required' => false, 'default' => 2 ) ),
        );
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        switch ( $operatorName )
        {
            case 'explayouts_safe_url':
                $operatorValue = self::url( $operatorValue );
                break;
            case 'explayouts_safe_embed_url':
                $operatorValue = self::embedUrl( $operatorValue );
                break;
            case 'explayouts_safe_color':
                $operatorValue = self::color( $operatorValue, (string)$namedParameters['fallback'] );
                break;
            case 'explayouts_safe_css_class':
                $operatorValue = self::cssClass( $operatorValue );
                break;
            case 'explayouts_safe_template_name':
                $operatorValue = self::templateName( $operatorValue );
                break;
            case 'explayouts_safe_tag':
                $operatorValue = self::headingTag( $operatorValue, $namedParameters['level'] );
                break;
        }
    }
}
