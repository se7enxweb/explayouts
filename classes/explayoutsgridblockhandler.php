<?php
class expLayoutsGridBlockHandler extends expLayoutsAbstractContentBlockHandler
{
    public function getParameters()
    {
        return $this->getDesignParameters();
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        $result = $this->fetchItems( $params, $block );
        $result['columns'] = isset( $params['number_of_columns'] ) ? (int)$params['number_of_columns'] : 3;
        return $result;
    }
}
