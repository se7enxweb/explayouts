<?php
class expLayoutsTabsBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'items' => array(
                'name' => 'Tabs (Title|Content per line)',
                'type' => 'textarea',
                'default' => '',
            ),
            'active_index' => array(
                'name' => 'Active tab index (0-based)',
                'type' => 'integer',
                'default' => 0,
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
            'active_index' => isset( $params['active_index'] ) ? (int)$params['active_index'] : 0,
        );
    }
}
