<?php
class expLayoutsAlertBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'message' => array(
                'name' => 'Message',
                'type' => 'textarea',
                'default' => '',
            ),
            'type' => array(
                'name' => 'Type',
                'type' => 'string',
                'default' => 'info',
            ),
            'dismissible' => array(
                'name' => 'Dismissible',
                'type' => 'integer',
                'default' => 0,
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        return array(
            'message' => isset( $params['message'] ) ? $params['message'] : '',
            'type' => isset( $params['type'] ) ? $params['type'] : 'info',
            'dismissible' => isset( $params['dismissible'] ) ? (int)$params['dismissible'] : 0,
        );
    }
}
