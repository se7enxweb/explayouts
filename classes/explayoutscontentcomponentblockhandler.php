<?php
/**
 * Handler for the content-backed component blocks (hero, features, about,
 * logos, quote, lead).
 *
 * These are the eZ4 equivalent of the reference's `ibexa_component` block
 * definition, whose view types are supplied per component content type by a
 * config provider. The block does not carry its own copy of the content: it
 * stores a reference in the `content` parameter and the matching content type
 * in `content_type_identifier`, and the templates render the referenced
 * component object.
 *
 * Distinct from expLayoutsComponentBlockHandler, which is the static variant
 * used by the palette's plain hero/about/features/lead blocks and keeps its
 * title, image and link in its own parameters.
 */
class expLayoutsContentComponentBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'content' => array(
                'name' => 'Component',
                'type' => 'browse',
                'default' => '',
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();

        return array(
            // Kept as stored. The reference records a Nexus content id here and
            // the templates resolve it through the component_content operator
            // (remote_id media-o-<id+776>, with fallbacks), so it must not be
            // rewritten to a local id.
            'content' => isset( $params['content'] ) ? $params['content'] : '',
            'content_type_identifier' => isset( $params['content_type_identifier'] )
                ? $params['content_type_identifier']
                : '',
        );
    }
}
