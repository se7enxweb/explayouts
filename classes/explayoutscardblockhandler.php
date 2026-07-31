<?php
class expLayoutsCardBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'title' => array(
                'name' => 'Title',
                'type' => 'string',
                'default' => '',
            ),
            'image_url' => array(
                'name' => 'Image URL',
                'type' => 'string',
                'default' => '',
            ),
            'content' => array(
                'name' => 'Content',
                'type' => 'textarea',
                'default' => '',
            ),
            'link_url' => array(
                'name' => 'Link URL',
                'type' => 'string',
                'default' => '',
            ),
            'link_label' => array(
                'name' => 'Link label',
                'type' => 'string',
                'default' => 'Read more',
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        return array(
            'title' => isset( $params['title'] ) ? $params['title'] : '',
            'image_url' => isset( $params['image_url'] ) ? $params['image_url'] : '',
            'content' => isset( $params['content'] ) ? $params['content'] : '',
            'link_url' => isset( $params['link_url'] ) ? $params['link_url'] : '',
            'link_label' => isset( $params['link_label'] ) ? $params['link_label'] : 'Read more',
        );
    }
}
