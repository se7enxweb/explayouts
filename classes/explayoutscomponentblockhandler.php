<?php
class expLayoutsComponentBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'title' => array(
                'name' => 'Title',
                'type' => 'text',
                'default' => '',
            ),
            'content' => array(
                'name' => 'Content',
                'type' => 'textarea',
                'default' => '',
            ),
            'image_url' => array(
                'name' => 'Image URL',
                'type' => 'string',
                'default' => '',
            ),
            'image_alt' => array(
                'name' => 'Image alt text',
                'type' => 'string',
                'default' => '',
            ),
            'link_text' => array(
                'name' => 'Link text',
                'type' => 'string',
                'default' => '',
            ),
            'link_url' => array(
                'name' => 'Link URL',
                'type' => 'string',
                'default' => '',
            ),
            'css_class' => array(
                'name' => 'CSS class',
                'type' => 'string',
                'default' => '',
            ),
            'css_id' => array(
                'name' => 'CSS ID',
                'type' => 'string',
                'default' => '',
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        return array(
            'title' => isset( $params['title'] ) ? $params['title'] : '',
            'content' => isset( $params['content'] ) ? $params['content'] : '',
            'image_url' => isset( $params['image_url'] ) ? $params['image_url'] : '',
            'image_alt' => isset( $params['image_alt'] ) ? $params['image_alt'] : '',
            'link_text' => isset( $params['link_text'] ) ? $params['link_text'] : '',
            'link_url' => isset( $params['link_url'] ) ? $params['link_url'] : '',
            'css_class' => isset( $params['css_class'] ) ? $params['css_class'] : '',
            'css_id' => isset( $params['css_id'] ) ? $params['css_id'] : '',
        );
    }
}
