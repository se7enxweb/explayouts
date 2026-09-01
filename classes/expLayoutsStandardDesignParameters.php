<?php

class expLayoutsStandardDesignParameters
{
    /**
     * Returns the standard set of block design parameters that are shown in the
     * right sidebar (query browser) for all block types. These mirror the
     * Nexus/Netgen Layouts editor: Use vertical whitespace, On top, On bottom,
     * CSS class, CSS ID, and Wrap in container.
     */
    public static function get()
    {
        return array(
            'vertical_whitespace:enabled' => array(
                'name' => 'Use vertical whitespace',
                'type' => 'compound_checkbox',
                'default' => '1',
                'children' => array(
                    'vertical_whitespace:top' => array(
                        'name' => 'On top',
                        'type' => 'select',
                        'options' => array(
                            'none' => 'None',
                            'small' => 'Small',
                            'medium' => 'Medium',
                            'large' => 'Large',
                        ),
                        'default' => 'none',
                    ),
                    'vertical_whitespace:bottom' => array(
                        'name' => 'On bottom',
                        'type' => 'select',
                        'options' => array(
                            'none' => 'None',
                            'small' => 'Small',
                            'medium' => 'Medium',
                            'large' => 'Large',
                        ),
                        'default' => 'medium',
                    ),
                ),
            ),
            'css_class' => array(
                'name' => 'CSS class',
                'type' => 'string',
                'default' => '',
            ),
            'css_id' => array(
                'name' => 'CSS ID',
                'type' => 'string',
                'default' => '',
            ),
            'set_container' => array(
                'name' => 'Wrap in container',
                'type' => 'compound_checkbox',
                'default' => '1',
                'children' => array(
                    'set_container:size' => array(
                        'name' => 'Container size',
                        'type' => 'select',
                        'options' => array(
                            '' => 'Regular',
                            'narrow' => 'Narrow',
                            'wide' => 'Wide',
                        ),
                        'default' => '',
                    ),
                ),
            ),
        );
    }
}
