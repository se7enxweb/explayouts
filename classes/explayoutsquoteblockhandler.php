<?php
class expLayoutsQuoteBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'quote' => array(
                'name' => 'Quote text',
                'type' => 'textarea',
                'default' => '',
            ),
            'author' => array(
                'name' => 'Author',
                'type' => 'string',
                'default' => '',
            ),
            'source' => array(
                'name' => 'Source',
                'type' => 'string',
                'default' => '',
            ),
            'align' => array(
                'name' => 'Alignment',
                'type' => 'string',
                'default' => 'left',
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        return array(
            'quote' => isset( $params['quote'] ) ? $params['quote'] : '',
            'author' => isset( $params['author'] ) ? $params['author'] : '',
            'source' => isset( $params['source'] ) ? $params['source'] : '',
            'align' => isset( $params['align'] ) ? $params['align'] : 'left',
        );
    }
}
