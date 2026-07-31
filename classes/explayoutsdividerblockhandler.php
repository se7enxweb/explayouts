<?php
class expLayoutsDividerBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'style' => array(
                'name' => 'Style',
                'type' => 'string',
                'default' => 'solid',
            ),
            'margin' => array(
                'name' => 'Margin in pixels',
                'type' => 'integer',
                'default' => 16,
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        return array(
            'style' => isset( $params['style'] ) ? $params['style'] : 'solid',
            'margin' => isset( $params['margin'] ) ? (int)$params['margin'] : 16,
        );
    }
}
