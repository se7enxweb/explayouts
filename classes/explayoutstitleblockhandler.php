<?php
class expLayoutsTitleBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
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
            'link' => array(
                'name' => 'Link',
                'type' => 'string',
                'default' => '',
            ),
            'use_link' => array(
                'name' => 'Use link',
                'type' => 'boolean',
                'default' => 0,
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        return array(
            'title' => isset( $params['title'] ) ? $params['title'] : '',
            'level' => isset( $params['level'] ) ? (int)$params['level'] : 2,
            'tag' => isset( $params['tag'] ) ? trim( $params['tag'] ) : '',
            'link' => isset( $params['link'] ) ? $params['link'] : '',
            'use_link' => isset( $params['use_link'] ) ? (int)$params['use_link'] : 0,
        );
    }
}
