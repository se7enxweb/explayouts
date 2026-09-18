<?php
/**
 * The link parameter shared by the blocks that can carry one.
 *
 * The reference installation renders a link as a compound checkbox - "Use
 * link" - whose children collapse when it is unchecked: a link type, the
 * value itself, a suffix, and "open in new window". Alpha declared the whole
 * thing as one string parameter, so the editor drew a single text input and
 * filled it with the stored JSON.
 *
 * The flattened storage follows the convention the design parameters already
 * use: the parent holds the value and the children are prefixed with it.
 *
 *   use_link          1 when the block links, '' or 0 when it does not
 *   link              the value - a URL, a path, an address or a number
 *   link:link_type    '', url, internal, email or phone
 *   link:link_suffix  appended to the href, for an anchor or a query string
 *   link:new_window   1 to open in a new window
 *
 * Keeping the value in `link` matters: ten title blocks already store a plain
 * path there and render correctly, and the front end reads that name.
 */
class expLayoutsLinkParameter
{
    const TYPE_NONE = '';
    const TYPE_URL = 'url';
    const TYPE_INTERNAL = 'internal';
    const TYPE_EMAIL = 'email';
    const TYPE_PHONE = 'phone';

    /**
     * The parameter definition, as a block handler's getParameters() wants it.
     *
     * $label lets the button block call it "Use link" too while keeping its
     * own wording if it ever needs to differ.
     */
    public static function definition( $label = 'Use link' )
    {
        return array(
            'use_link' => array(
                'name' => $label,
                'type' => 'compound_checkbox',
                'default' => '0',
                'children' => array(
                    'link:link_type' => array(
                        'name' => 'Link type',
                        'type' => 'select',
                        'options' => array(
                            self::TYPE_NONE => 'No link',
                            self::TYPE_URL => 'URL',
                            self::TYPE_INTERNAL => 'Relative URL',
                            self::TYPE_EMAIL => 'E-mail',
                            self::TYPE_PHONE => 'Phone number',
                        ),
                        'default' => self::TYPE_NONE,
                    ),
                    'link' => array(
                        'name' => 'Link',
                        'type' => 'string',
                        'default' => '',
                    ),
                    'link:link_suffix' => array(
                        'name' => 'Link suffix',
                        'type' => 'string',
                        'default' => '',
                    ),
                    'link:new_window' => array(
                        'name' => 'Open in new window',
                        'type' => 'checkbox',
                        'default' => '0',
                    ),
                ),
            ),
        );
    }

    /**
     * Read a stored link out of a block's parameters.
     *
     * Returns href, target and enabled, so a template does not have to know
     * how a phone number differs from an internal path. A value still held as
     * the old JSON is understood, so a block that has not been migrated
     * renders as a link rather than as its own JSON.
     */
    public static function resolve( array $params )
    {
        $enabled = isset( $params['use_link'] ) && (string)$params['use_link'] === '1';
        $value = isset( $params['link'] ) ? (string)$params['link'] : '';
        $type = isset( $params['link:link_type'] ) ? (string)$params['link:link_type'] : '';
        $suffix = isset( $params['link:link_suffix'] ) ? (string)$params['link:link_suffix'] : '';
        $newWindow = isset( $params['link:new_window'] ) && (string)$params['link:new_window'] === '1';

        // A value still in the old shape carries its own type and suffix.
        $decoded = self::decodeLegacy( $value );
        if ( $decoded !== false )
        {
            $value = $decoded['link'];
            if ( $type === '' )
                $type = $decoded['link_type'];
            if ( $suffix === '' )
                $suffix = $decoded['link_suffix'];
            if ( !$newWindow )
                $newWindow = $decoded['new_window'];
        }

        $href = self::href( $type, $value, $suffix );

        return array(
            'enabled' => $enabled && $href !== '',
            'href' => $href,
            'target' => $newWindow ? '_blank' : '',
            'link_type' => $type,
        );
    }

    /**
     * The JSON the reference installation exported for a link, or false when
     * the value is a plain one.
     */
    public static function decodeLegacy( $value )
    {
        $value = trim( (string)$value );
        if ( $value === '' || $value[0] !== '{' )
            return false;

        $data = json_decode( $value, true );
        if ( !is_array( $data ) || !array_key_exists( 'link', $data ) )
            return false;

        return array(
            'link_type' => isset( $data['link_type'] ) && $data['link_type'] !== null ? (string)$data['link_type'] : '',
            'link' => isset( $data['link'] ) && $data['link'] !== null ? (string)$data['link'] : '',
            'link_suffix' => isset( $data['link_suffix'] ) && $data['link_suffix'] !== null ? (string)$data['link_suffix'] : '',
            'new_window' => !empty( $data['new_window'] ),
        );
    }

    /**
     * Build the href for a type and value. An empty value yields an empty
     * href, so a block with "use link" ticked but nothing filled in renders
     * as plain text rather than as a link to nowhere.
     */
    public static function href( $type, $value, $suffix = '' )
    {
        $value = trim( (string)$value );
        if ( $value === '' )
            return '';

        switch ( (string)$type )
        {
            case self::TYPE_EMAIL:
                $href = strpos( $value, 'mailto:' ) === 0 ? $value : 'mailto:' . $value;
                break;

            case self::TYPE_PHONE:
                $href = strpos( $value, 'tel:' ) === 0 ? $value : 'tel:' . $value;
                break;

            case self::TYPE_INTERNAL:
                $href = self::internalHref( $value );
                break;

            case self::TYPE_URL:
            default:
                $href = $value;
                break;
        }

        if ( $href === '' )
            return '';

        $suffix = trim( (string)$suffix );
        return $suffix === '' ? $href : $href . $suffix;
    }

    /**
     * An internal link, which may still be written the way the reference
     * installation writes one.
     *
     * A location reference is resolved through the node it names; an
     * unresolvable one yields an empty href rather than a dead
     * "ibexa-location://" address in the markup.
     */
    protected static function internalHref( $value )
    {
        if ( preg_match( '#^(?:ibexa|ezlocation|ezcontent)-?location://(\d+)$#i', $value, $matches )
            || preg_match( '#^(?:ibexa|ez)location://(\d+)$#i', $value, $matches ) )
        {
            return self::nodeHref( (int)$matches[1] );
        }

        if ( preg_match( '#^(?:ibexa|ez)content://(\d+)$#i', $value, $matches ) )
        {
            $object = eZContentObject::fetch( (int)$matches[1] );
            return $object ? self::nodeHref( (int)$object->attribute( 'main_node_id' ) ) : '';
        }

        if ( ctype_digit( $value ) )
            return self::nodeHref( (int)$value );

        // Already a path.
        return $value[0] === '/' ? $value : '/' . $value;
    }

    protected static function nodeHref( $nodeId )
    {
        if ( $nodeId <= 0 )
            return '';

        $node = eZContentObjectTreeNode::fetch( $nodeId );
        if ( !$node )
            return '';

        $alias = $node->attribute( 'url_alias' );
        return $alias !== null && $alias !== '' ? '/' . ltrim( (string)$alias, '/' ) : '';
    }
}
