<?php
class expLayoutsButtonBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'text' => array(
                'name' => 'Text',
                'type' => 'string',
                'default' => '',
            ),
            'label' => array(
                'name' => 'Label (legacy)',
                'type' => 'string',
                'default' => '',
            ),
            'link' => array(
                'name' => 'Link',
                'type' => 'string',
                'default' => '',
            ),
            'url' => array(
                'name' => 'URL (legacy)',
                'type' => 'string',
                'default' => '',
            ),
            'style' => array(
                'name' => 'Style',
                'type' => 'string',
                'default' => 'default_button',
            ),
            'target' => array(
                'name' => 'Target',
                'type' => 'string',
                'default' => '_self',
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();

        $text = '';
        if ( isset( $params['text'] ) && $params['text'] !== '' )
            $text = $params['text'];
        elseif ( isset( $params['label'] ) )
            $text = $params['label'];

        $link = '';
        if ( isset( $params['link'] ) && $params['link'] !== '' )
            $link = $params['link'];
        elseif ( isset( $params['url'] ) )
            $link = $params['url'];

        $style = isset( $params['style'] ) ? $params['style'] : 'default_button';
        $class = '';
        switch ( $style )
        {
            case 'default_button':
                $class = 'btn btn-default';
                break;
            case 'highlighted_button':
            case 'primary':
                $class = 'btn btn-primary';
                break;
            case 'link':
                $class = 'link';
                break;
            default:
                $class = $style;
        }

        return array(
            'text' => $text,
            'label' => $text,
            'link' => $link,
            'url' => $link,
            'style' => $style,
            'class' => $class,
            'target' => isset( $params['target'] ) ? $params['target'] : '_self',
        );
    }
}
