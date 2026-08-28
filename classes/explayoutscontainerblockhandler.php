<?php
class expLayoutsContainerBlockHandler implements expLayoutsBlockHandlerInterface
{
    public function getParameters()
    {
        return array(
            'background_color:enabled' => array(
                'name' => 'Set background color',
                'type' => 'compound_checkbox',
                'default' => '',
                'children' => array(
                    'background_color:color' => array(
                        'name' => 'Background color',
                        'type' => 'select',
                        'options' => array(
                            'primary' => 'Primary (Yellow)',
                            'secondary' => 'Secondary (Light gray)',
                            'white' => 'White',
                            'black' => 'Black',
                        ),
                        'default' => 'secondary',
                    ),
                ),
            ),
            'background_image:enabled' => array(
                'name' => 'Set background image',
                'type' => 'compound_checkbox',
                'default' => '',
                'children' => array(
                    'background_image:image' => array(
                        'name' => 'Background image',
                        'type' => 'browse',
                        'default' => '',
                    ),
                ),
            ),
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

    public function getValues( $block )
    {
        return array();
    }
}
