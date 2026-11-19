<?php
class expLayoutsTplBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'block_name' => array(
                'name' => 'Template block name',
                'type' => 'text',
                'default' => '',
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        return array(
            // the name of design:explayouts/tpl_block/<block_name>.tpl: letters, digits and _ only, no path
            'block_name' => isset( $params['block_name'] ) ? expLayoutsSafeValue::templateName( $params['block_name'] ) : '',
        );
    }
}
