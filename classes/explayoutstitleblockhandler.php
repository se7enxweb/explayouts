<?php
class expLayoutsTitleBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        $parameters = array(
            'title' => array(
                'name' => 'Title',
                'type' => 'string',
                'default' => '',
            ),
            'level' => array(
                'name' => 'Heading level (1-6)',
                'type' => 'integer',
                'default' => 2,
            ),
            'tag' => array(
                'name' => 'HTML tag',
                'type' => 'string',
                'default' => '',
            ),
        );

        // The link is a compound field. Declared as a string plus a boolean it
        // drew two text inputs - one holding the stored JSON, one holding the
        // on/off value - neither of which a person can use.
        return array_merge( $parameters, expLayoutsLinkParameter::definition( 'Use link' ) );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        $values = array(
            'title' => isset( $params['title'] ) ? $params['title'] : '',
            'level' => isset( $params['level'] ) ? (int)$params['level'] : 2,
            'tag' => isset( $params['tag'] ) ? trim( $params['tag'] ) : '',
            'link' => isset( $params['link'] ) ? $params['link'] : '',
            'use_link' => isset( $params['use_link'] ) ? (int)$params['use_link'] : 0,
        );

        // Resolved once here so a template need not know how a phone number
        // differs from an internal path, nor how to read a value still held in
        // the old JSON shape.
        $link = expLayoutsLinkParameter::resolve( $params );
        $values['link_href'] = $link['href'];
        $values['link_target'] = $link['target'];
        $values['has_link'] = $link['enabled'];

        return $values;
    }
}
