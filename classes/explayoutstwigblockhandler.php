<?php
class expLayoutsTwigBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'block_name' => array(
                'name' => 'Template name',
                'type' => 'text',
                'default' => '',
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        return array(
            'block_name' => isset( $params['block_name'] ) ? (string)$params['block_name'] : '',
        );
    }
}
