<?php
class expLayoutsFixtures
{
    static function installBaseData()
    {
        if ( expLayoutsLayout::fetchByIdentifier( 'default_homepage', 1 ) )
            return array( 'message' => 'Base data already installed.' );

        $layout = expLayoutsLayout::create( 'default_homepage', 'Default Homepage', '1_column' );
        $layout->store();
        $layoutId = (int)$layout->attribute( 'id' );

        $zone = expLayoutsZone::create( $layoutId, 'main', 1 );
        $zone->setAttribute( 'position', 0 );
        $zone->store();
        $zoneId = (int)$zone->attribute( 'id' );

        $title = expLayoutsBlock::create( $zoneId, $layoutId, 'title', 'Welcome' );
        $title->setAttribute( 'position', 0 );
        $title->store();
        expLayoutsBlockParameter::set( (int)$title->attribute( 'id' ), 'title', 'Welcome' );
        expLayoutsBlockParameter::set( (int)$title->attribute( 'id' ), 'level', '1' );

        $text = expLayoutsBlock::create( $zoneId, $layoutId, 'text', 'Introduction' );
        $text->setAttribute( 'position', 1 );
        $text->store();
        expLayoutsBlockParameter::set( (int)$text->attribute( 'id' ), 'content', 'This is the default homepage layout.' );

        $rule = expLayoutsRule::create( $layoutId, 0 );
        $rule->store();
        $ruleId = (int)$rule->attribute( 'id' );

        $target = expLayoutsRuleTarget::create( $ruleId, 'path_prefix', '' );
        $target->store();

        return array( 'message' => 'Base data installed (layout ' . $layoutId . ').' );
    }

    static function installTestData()
    {
        $messages = array();

        if ( !expLayoutsLayout::fetchByIdentifier( 'test_homepage', 1 ) )
        {
            $l1 = self::createLayout( 'test_homepage', 'Test Homepage', 'hero', array(
                'top' => array(
                    array( 'title', 'Homepage title', array( 'title' => 'Welcome to the test site', 'level' => '1' ) ),
                ),
                'main' => array(
                    array( 'text', 'Intro text', array( 'content' => 'This is a demo homepage built from the legacy extension test data.' ) ),
                ),
                'right' => array(
                    array( 'image', 'Hero image', array(
                        'image_url' => 'https://via.placeholder.com/300x200',
                        'alt' => 'Demo image',
                    ) ),
                ),
            ) );
            self::createRule( $l1, 10, array( array( 'path_prefix', '/home' ) ) );
            $messages[] = 'Created test_homepage (layout ' . $l1 . ').';
        }

        if ( !expLayoutsLayout::fetchByIdentifier( 'test_products', 1 ) )
        {
            $l2 = self::createLayout( 'test_products', 'Test Products', '2_column', array(
                'left' => array(
                    array( 'text', 'Product description', array( 'content' => 'Product details and specifications go here.' ) ),
                ),
                'right' => array(
                    array( 'button', 'Buy now', array( 'label' => 'Buy now', 'url' => '/buy', 'style' => 'primary' ) ),
                ),
            ) );
            self::createRule( $l2, 20, array( array( 'path_prefix', '/products' ) ) );
            $messages[] = 'Created test_products (layout ' . $l2 . ').';
        }

        if ( !expLayoutsLayout::fetchByIdentifier( 'test_blog', 1 ) )
        {
            $l3 = self::createLayout( 'test_blog', 'Test Blog', 'sidebar_right', array(
                'main' => array(
                    array( 'title', 'Blog title', array( 'title' => 'Latest posts', 'level' => '2' ) ),
                ),
                'sidebar' => array(
                    array( 'text', 'Sidebar text', array( 'content' => 'Sidebar content for the blog layout.' ) ),
                ),
            ) );
            self::createRule( $l3, 30, array( array( 'path_prefix', '/blog' ) ) );
            $messages[] = 'Created test_blog (layout ' . $l3 . ').';
        }

        if ( count( $messages ) === 0 )
            return array( 'message' => 'Test data already installed.' );

        return array( 'message' => implode( "\n", $messages ) );
    }

    private static function createLayout( $identifier, $name, $layoutType, array $zones )
    {
        $layout = expLayoutsLayout::create( $identifier, $name, $layoutType );
        $layout->store();
        $layoutId = (int)$layout->attribute( 'id' );

        $position = 0;
        foreach ( $zones as $zoneIdentifier => $blocks )
        {
            $zone = expLayoutsZone::create( $layoutId, $zoneIdentifier, 1 );
            $zone->setAttribute( 'position', $position );
            $zone->store();
            $zoneId = (int)$zone->attribute( 'id' );

            $blockPosition = 0;
            foreach ( $blocks as $blockData )
            {
                $definition = $blockData[0];
                $blockName = $blockData[1];
                $parameters = isset( $blockData[2] ) ? $blockData[2] : array();

                $block = expLayoutsBlock::create( $zoneId, $layoutId, $definition, $blockName );
                $block->setAttribute( 'position', $blockPosition );
                $block->store();
                $blockId = (int)$block->attribute( 'id' );

                foreach ( $parameters as $paramName => $paramValue )
                {
                    expLayoutsBlockParameter::set( $blockId, $paramName, $paramValue );
                }

                $blockPosition++;
            }

            $position++;
        }

        return $layoutId;
    }

    private static function createRule( $layoutId, $priority, array $targets, array $conditions = array() )
    {
        $rule = expLayoutsRule::create( $layoutId, $priority );
        $rule->store();
        $ruleId = (int)$rule->attribute( 'id' );

        foreach ( $targets as $targetData )
        {
            $target = expLayoutsRuleTarget::create( $ruleId, $targetData[0], $targetData[1] );
            $target->store();
        }

        foreach ( $conditions as $conditionData )
        {
            $condition = expLayoutsRuleCondition::create( $ruleId, $conditionData[0], $conditionData[1] );
            $condition->store();
        }

        return $ruleId;
    }
}
