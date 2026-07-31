<?php

class expLayoutsSingleBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'node_id' => array(
                'name' => 'Node ID',
                'type' => 'integer',
                'default' => 0,
            ),
            'show_name' => array(
                'name' => 'Show name',
                'type' => 'boolean',
                'default' => 1,
            ),
            'show_intro' => array(
                'name' => 'Show intro',
                'type' => 'boolean',
                'default' => 0,
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        $nodeId = isset( $params['node_id'] ) ? (int)$params['node_id'] : 0;
        $node = $nodeId > 0 ? expLayoutsSiteAPI::loadLocation( $nodeId ) : false;

        $name = '';
        $intro = '';
        $link = '';
        if ( $node )
        {
            if ( isset( $params['show_name'] ) && (int)$params['show_name'] )
                $name = (string)$node->attribute( 'name' );
            if ( isset( $params['show_intro'] ) && (int)$params['show_intro'] )
            {
                $dataMap = $node->attribute( 'data_map' );
                if ( isset( $dataMap['intro'] ) && is_object( $dataMap['intro'] ) )
                {
                    $introContent = $dataMap['intro']->content();
                    $intro = is_object( $introContent ) && method_exists( $introContent, 'render' ) ? (string)$introContent->render() : (string)$introContent;
                }
            }
            $link = $node->attribute( 'url_alias' );
        }

        return array(
            'name' => $name,
            'intro' => $intro,
            'link' => $link,
        );
    }
}
