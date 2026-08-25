<?php
class expLayoutsRichTextBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'content' => array(
                'name' => 'Content',
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
