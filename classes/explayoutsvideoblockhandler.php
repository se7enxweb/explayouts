<?php
class expLayoutsVideoBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'video_url' => array(
                'name' => 'Video URL or embed src',
                'type' => 'string',
                'default' => '',
            ),
            'width' => array(
                'name' => 'Width (px or %)',
                'type' => 'string',
                'default' => '100%',
            ),
            'height' => array(
                'name' => 'Height (px)',
                'type' => 'integer',
                'default' => 360,
            ),
            'autoplay' => array(
                'name' => 'Autoplay',
                'type' => 'integer',
                'default' => 0,
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        return array(
            'video_url' => isset( $params['video_url'] ) ? $params['video_url'] : '',
            'width' => isset( $params['width'] ) ? $params['width'] : '100%',
            'height' => isset( $params['height'] ) ? (int)$params['height'] : 360,
            'autoplay' => isset( $params['autoplay'] ) ? (int)$params['autoplay'] : 0,
        );
    }
}
