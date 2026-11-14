<?php
class expLayoutsCarouselBlockHandler extends expLayoutsAbstractContentBlockHandler
{
    public function getParameters()
    {
        $params = $this->getDesignParameters();
        unset( $params['number_of_columns'] );
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
                if ( is_array( $dataMap ) && isset( $dataMap[$result['image_attribute']] ) )
                {
                    $attr = $dataMap[$result['image_attribute']];
                    $image = is_object( $attr ) && $attr->hasAttribute( 'content' ) ? $attr->attribute( 'content' ) : false;
                    // Only an image alias handler has an original: an attribute
                    // of another type under the same identifier has an array as
                    // content, and attribute() on it was "Call to a member
                    // function attribute() on array".
                    if ( is_object( $image ) && method_exists( $image, 'attribute' ) )
                    {
                        $original = $image->attribute( 'original' );
                        $url = is_array( $original ) && isset( $original['url'] ) ? (string)$original['url'] : '';

                        // Alias URLs carry no leading slash, so an <img src>
                        // built from one resolves against the current page -
                        // on /testing/ it became /testing/var/site/... and 404'd;
                        // with the web root in front, as ezroot writes it, an
                        // installation served from a sub path finds it too.
                        $url = expLayoutsSafeValue::webRootUrl( $url );
                    }
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
