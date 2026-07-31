<?php
class expLayoutsBadgeBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'label' => array(
                'name' => 'Label',
                'type' => 'string',
                'default' => '',
            ),
            'style' => array(
                'name' => 'Style',
                'type' => 'string',
                'default' => 'primary',
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
            'label' => isset( $params['label'] ) ? $params['label'] : '',
            'style' => isset( $params['style'] ) ? $params['style'] : 'primary',
            'link' => isset( $params['link'] ) ? $params['link'] : '',
        );
    }
}
