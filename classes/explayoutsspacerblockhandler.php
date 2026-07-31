<?php
class expLayoutsSpacerBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'height' => array(
                'name' => 'Height in pixels',
                'type' => 'integer',
                'default' => 32,
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        return array(
            'height' => isset( $params['height'] ) ? (int)$params['height'] : 32,
        );
    }
}
