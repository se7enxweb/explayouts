<?php
/**
 * The handler of the admin blocks that take no parameter (design admin4l): the
 * block shows one part of the admin page, drawn by its view template
 * design:explayouts/block/<definition identifier>.tpl from admin4's own template.
 */
class expLayoutsAdminPartBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array();
    }

    public function getValues( $block )
    {
        return array();
    }
}
