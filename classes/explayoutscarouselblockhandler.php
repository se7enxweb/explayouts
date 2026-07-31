<?php
class expLayoutsCarouselBlockHandler extends expLayoutsAbstractContentBlockHandler
{
    public function getParameters()
    {
        $params = $this->getCommonParameters();
        $params['slides_per_view'] = array(
            'name' => 'Slides per view',
            'type' => 'integer',
            'default' => 1,
        );
        $params['autoplay'] = array(
            'name' => 'Autoplay (ms, 0 to disable)',
            'type' => 'integer',
            'default' => 0,
        );
        $params['image_attribute'] = array(
            'name' => 'Image attribute identifier',
            'type' => 'string',
            'default' => 'image',
        );
        return $params;
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        $result = $this->fetchItems( $params, $block );
        $result['slides_per_view'] = isset( $params['slides_per_view'] ) ? (int)$params['slides_per_view'] : 1;
        $result['autoplay'] = isset( $params['autoplay'] ) ? (int)$params['autoplay'] : 0;
        $result['image_attribute'] = isset( $params['image_attribute'] ) ? $params['image_attribute'] : 'image';

        $slides = array();
        foreach ( $result['items'] as $node )
        {
            $object = $node->attribute( 'object' );
            $url = '';
            if ( $object )
            {
                $dataMap = $object->attribute( 'data_map' );
                if ( isset( $dataMap[$result['image_attribute']] ) )
                {
                    $attr = $dataMap[$result['image_attribute']];
                    $image = $attr->hasAttribute( 'content' ) ? $attr->attribute( 'content' ) : false;
                    if ( $image )
                        $url = $image->attribute( 'original' )['url'];
                }
            }
            $slides[] = array(
                'node' => $node,
                'image_url' => $url,
            );
        }
        $result['slides'] = $slides;

        return $result;
    }
}
