<?php
class expLayoutsGridBlockHandler extends expLayoutsAbstractContentBlockHandler
{
    public function getParameters()
    {
        $params = $this->getCommonParameters();
        $params['columns'] = array(
            'name' => 'Columns',
            'type' => 'integer',
            'default' => 3,
        );
        return $params;
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        $result = $this->fetchItems( $params, $block );
        $result['columns'] = isset( $params['columns'] ) ? (int)$params['columns'] : 3;
        return $result;
    }
}
