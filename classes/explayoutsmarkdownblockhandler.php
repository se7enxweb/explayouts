<?php
class expLayoutsMarkdownBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'content' => array(
                'name' => 'Markdown content',
                'type' => 'textarea',
                'default' => '',
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        $content = isset( $params['content'] ) ? $params['content'] : '';
        $html = nl2br( htmlspecialchars( $content, ENT_QUOTES, 'UTF-8' ), false );
        return array(
            'content' => $content,
            'html' => $html,
        );
    }
}
