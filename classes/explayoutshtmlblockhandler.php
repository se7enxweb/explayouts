<?php
class expLayoutsHtmlBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'content' => array(
                'name' => 'Raw HTML',
                'type' => 'textarea',
                'default' => '',
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        return array(
            'content' => isset( $params['content'] ) ? $params['content'] : '',
        );
    }
}
