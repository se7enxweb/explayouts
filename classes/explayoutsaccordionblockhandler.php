<?php
class expLayoutsAccordionBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'items' => array(
                'name' => 'Items (Title|Content per line)',
                'type' => 'textarea',
                'default' => '',
            ),
            'open_first' => array(
                'name' => 'Open first item',
                'type' => 'boolean',
                'default' => 1,
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        $raw = isset( $params['items'] ) ? $params['items'] : '';
        $items = array();
        foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line )
        {
            $parts = explode( '|', $line, 2 );
            if ( count( $parts ) === 2 )
            {
                $items[] = array(
                    'title' => trim( $parts[0] ),
                    'content' => trim( $parts[1] ),
                );
            }
        }

        return array(
            'items' => $items,
            'open_first' => isset( $params['open_first'] ) ? (int)$params['open_first'] : 1,
        );
    }
}
