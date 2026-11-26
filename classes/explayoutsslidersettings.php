<?php
/**
 * The settings of a slider from a block's settings in the Design tab, for the slider of any design: slick's own
 * settings (data-slick) and the classes, computed in PHP so a slider costs microseconds, not a template's worth of
 * operators.
 *
 *   <div{include uri='design:explayouts/block/parts/slick_attributes.tpl' block=$block}>   (the usual way)
 *   {def $s = $block.parameters|explayouts_slider_settings( 2, 2 )}                         ($s.json, $s.dots, $s.class)
 *
 * The block's parameters:
 *  - number_of_thumbnails: items per slide (slidesToShow), $perView when none; at most $perViewMd up to 991px wide and
 *    one up to 480px
 *  - next_and_previous: arrows, shown when the block stores nothing
 *  - show_pagination: dots, and the class has-dots (room for them under the slides)
 *  - infinite_loop: infinite, off when nothing is stored
 *  - transition = fade: fade, which slick can do with one item per slide only, else the items slide
 *  - autoplay, autoplay_time: autoplay every 1 to 12 seconds, 5 when none or another value is stored
 */
class expLayoutsSliderSettings
{
    /**
     * @param array $parameters the block's parameters (name => stored string)
     * @param int $perView items per slide when the block stores none
     * @param int $perViewMd the most items per slide up to 991px wide
     * @return array json (slick settings; only numbers and booleans, safe inside a single-quoted attribute), dots
     *               (bool), class ("slick-slider" plus " has-dots")
     */
    public static function settings( $parameters, $perView = 2, $perViewMd = 2 )
    {
        $p = is_array( $parameters ) ? $parameters : array();
        $flag = function ( $name ) use ( $p ) { return isset( $p[$name] ) && (string)$p[$name] === '1'; };
        $n = isset( $p['number_of_thumbnails'] ) ? (int)$p['number_of_thumbnails'] : 0;
        if ( $n < 1 )
            $n = max( 1, (int)$perView );
        $nMd = max( 1, min( $n, (int)$perViewMd ) );
        $time = isset( $p['autoplay_time'] ) ? (int)$p['autoplay_time'] : 0;
        if ( $time < 1 || $time > 12 )
            $time = 5;
        $dots = $flag( 'show_pagination' );
        $settings = array(
            'slidesToShow' => $n,
            'arrows' => array_key_exists( 'next_and_previous', $p ) ? $flag( 'next_and_previous' ) : true,
            'dots' => $dots,
            'infinite' => $flag( 'infinite_loop' ),
            'fade' => isset( $p['transition'] ) && (string)$p['transition'] === 'fade' && $n === 1,
            'autoplay' => $flag( 'autoplay' ),
            'autoplaySpeed' => $time * 1000,
            'responsive' => array(
                array( 'breakpoint' => 991, 'settings' => array( 'slidesToShow' => $nMd ) ),
                array( 'breakpoint' => 480, 'settings' => array( 'slidesToShow' => 1 ) ),
            ),
        );
        return array( 'json' => json_encode( $settings, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP ),
                      'dots' => $dots,
                      'class' => 'slick-slider' . ( $dots ? ' has-dots' : '' ) );
    }

    // ------------------------------------------------------------------ the template operator

    public function operatorList()
    {
        return array( 'explayouts_slider_settings' );
    }

    public function namedParameterPerOperator()
    {
        return true;
    }

    public function namedParameterList()
    {
        return array( 'explayouts_slider_settings' => array(
            'per_view' => array( 'type' => 'integer', 'required' => false, 'default' => 2 ),
            'per_view_md' => array( 'type' => 'integer', 'required' => false, 'default' => 2 ),
        ) );
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        $operatorValue = self::settings( $operatorValue, $namedParameters['per_view'], $namedParameters['per_view_md'] );
    }
}
