<?php
class expLayoutsListBlockHandler extends expLayoutsAbstractContentBlockHandler
{
    public function getParameters()
    {
        return $this->getCommonParameters();
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        return $this->fetchItems( $params, $block );
    }
}
