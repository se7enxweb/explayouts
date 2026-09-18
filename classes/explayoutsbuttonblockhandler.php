<?php
class expLayoutsButtonBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        $parameters = array(
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

        // Same compound link the title block uses. Declared as a string it
        // drew a text input holding the stored JSON.
        return array_merge( $parameters, expLayoutsLinkParameter::definition( 'Use link' ) );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();

        $text = '';
        if ( isset( $params['text'] ) && $params['text'] !== '' )
            $text = $params['text'];
        elseif ( isset( $params['label'] ) )
            $text = $params['label'];

        // The compound link wins; the legacy url parameter is the fallback for
        // a button that predates it.
        $resolved = expLayoutsLinkParameter::resolve( $params );
        $link = $resolved['href'];
        if ( $link === '' && isset( $params['url'] ) )
            $link = (string)$params['url'];

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
            // "Open in new window" on the compound link wins over the older
            // free-text target, which stays for buttons that still carry one.
            'target' => $resolved['target'] !== ''
                ? $resolved['target']
                : ( isset( $params['target'] ) ? $params['target'] : '_self' ),
        );
    }
}
