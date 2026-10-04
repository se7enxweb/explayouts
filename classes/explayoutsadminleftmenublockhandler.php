<?php
/**
 * The left menu block of the admin layouts. Without a parameter it shows the menu
 * of the navigation part of the page (as admin4 does); "part" names one instead
 * (content, setup, my, user, shop ... = design:parts/<part>/menu.tpl).
 */
class expLayoutsAdminLeftMenuBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'part' => array(
                'name' => 'Navigation part (empty: the one of the page)',
                'type' => 'string',
                'default' => '',
            ),
        );
    }

    public function getValues( $block )
    {
        $params = is_array( $block ) && isset( $block['parameters'] ) ? $block['parameters'] : array();
        $part = isset( $params['part'] ) ? (string)$params['part'] : '';
        // a part name is a directory name: nothing else reaches the template path
        if ( !preg_match( '/^[a-z0-9_]*$/', $part ) )
            $part = '';
        return array( 'part' => $part );
    }
}
