<?php
class expLayoutsImageBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'image_url' => array(
                'name' => 'Image URL',
                'type' => 'string',
                'default' => '',
            ),
            'alt' => array(
                'name' => 'Alt text',
                'type' => 'string',
                'default' => '',
            ),
            'link' => array(
                'name' => 'Link URL',
                'type' => 'string',
                'default' => '',
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        return array(
            'image_url' => isset( $params['image_url'] ) ? $params['image_url'] : '',
            'alt' => isset( $params['alt'] ) ? $params['alt'] : '',
            'link' => isset( $params['link'] ) ? $params['link'] : '',
        );
    }
}
