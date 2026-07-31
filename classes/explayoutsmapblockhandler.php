<?php
class expLayoutsMapBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'embed_url' => array(
                'name' => 'Map embed URL',
                'type' => 'string',
                'default' => '',
            ),
            'width' => array(
                'name' => 'Width',
                'type' => 'string',
                'default' => '100%',
            ),
            'height' => array(
                'name' => 'Height (px)',
                'type' => 'integer',
                'default' => 400,
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        return array(
            'embed_url' => isset( $params['embed_url'] ) ? $params['embed_url'] : '',
            'width' => isset( $params['width'] ) ? $params['width'] : '100%',
            'height' => isset( $params['height'] ) ? (int)$params['height'] : 400,
        );
    }
}
