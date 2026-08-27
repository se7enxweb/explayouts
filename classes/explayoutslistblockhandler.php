<?php
class expLayoutsListBlockHandler extends expLayoutsAbstractContentBlockHandler
{
    public function getParameters()
    {
        return $this->getDesignParameters();
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        return $this->fetchItems( $params, $block );
    }
}
