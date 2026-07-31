<?php
class expLayoutsProgressBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'value' => array(
                'name' => 'Value (0-100)',
                'type' => 'integer',
                'default' => 0,
            ),
            'label' => array(
                'name' => 'Label',
                'type' => 'string',
                'default' => '',
            ),
            'color' => array(
                'name' => 'Color',
                'type' => 'string',
                'default' => '#38bdf8',
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        $value = isset( $params['value'] ) ? (int)$params['value'] : 0;
        if ( $value < 0 )
            $value = 0;
        if ( $value > 100 )
            $value = 100;

        return array(
            'value' => $value,
            'label' => isset( $params['label'] ) ? $params['label'] : '',
            'color' => isset( $params['color'] ) ? $params['color'] : '#38bdf8',
        );
    }
}
