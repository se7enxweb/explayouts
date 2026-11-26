<?php
/**
 * The settings of a slider from a block's Design tab (expLayoutsSliderSettings), without the kernel.
 *
 * Run directly with:
 *   php tests/explayoutsslidersettingstest.php
 *
 *  SL-01 - nothing stored: two per slide, arrows, no dots, no loop, no fade, no autoplay, 5 s
 *  SL-02 - every setting reaches slick; has-dots with pagination
 *  SL-03 - fade only with one item per slide; the time is 1 to 12 s, else 5; per_view and per_view_md
 *  SL-04 - the JSON holds numbers and booleans only, so it is safe inside a single-quoted attribute
 *  SL-05 - the operator is registered and the standard part template uses it, with no inline style
 *  SL-06 - cost: a slider's settings in microseconds
 */

$failures = 0;
function slCheck( $ok, $message )
{
    global $failures;
    echo ( $ok ? 'PASS' : 'FAIL' ), ": $message\n";
    if ( !$ok )
        $failures++;
}

$root = dirname( __DIR__ );
require_once $root . '/classes/explayoutsslidersettings.php';
$s = function ( $p, $perView = 2, $perViewMd = 2 ) {
    $r = expLayoutsSliderSettings::settings( $p, $perView, $perViewMd );
    $r['data'] = json_decode( $r['json'], true );
    return $r;
};

// SL-01
$r = $s( array() );
slCheck( $r['data']['slidesToShow'] === 2 && $r['data']['arrows'] === true && $r['data']['dots'] === false && $r['data']['infinite'] === false
         && $r['data']['fade'] === false && $r['data']['autoplay'] === false && $r['data']['autoplaySpeed'] === 5000, 'SL-01 defaults' );
slCheck( $r['class'] === 'slick-slider' && $r['dots'] === false, 'SL-01 class without dots' );
slCheck( $s( null )['data']['slidesToShow'] === 2, 'SL-01 no parameters at all' );

// SL-02
$r = $s( array( 'number_of_thumbnails' => '1', 'next_and_previous' => '0', 'show_pagination' => '1', 'infinite_loop' => '1',
                'transition' => 'fade', 'autoplay' => '1', 'autoplay_time' => '3' ) );
slCheck( $r['data']['slidesToShow'] === 1 && $r['data']['arrows'] === false && $r['data']['dots'] === true && $r['data']['infinite'] === true
         && $r['data']['fade'] === true && $r['data']['autoplay'] === true && $r['data']['autoplaySpeed'] === 3000, 'SL-02 every setting' );
slCheck( $r['class'] === 'slick-slider has-dots' && $r['dots'] === true, 'SL-02 has-dots' );
slCheck( $s( array( 'next_and_previous' => '' ) )['data']['arrows'] === false, 'SL-02 arrows stored as off stay off' );

// SL-03
slCheck( $s( array( 'number_of_thumbnails' => '3', 'transition' => 'fade' ) )['data']['fade'] === false, 'SL-03 no fade with three per slide' );
foreach ( array( '0' => 5000, '1' => 1000, '12' => 12000, '13' => 5000, 'x' => 5000, '-4' => 5000 ) as $in => $ms )
    slCheck( $s( array( 'autoplay_time' => (string)$in ) )['data']['autoplaySpeed'] === $ms, "SL-03 autoplay_time '$in' => $ms ms" );
$r = $s( array( 'number_of_thumbnails' => '6' ), 2, 3 );
slCheck( $r['data']['slidesToShow'] === 6 && $r['data']['responsive'][0]['settings']['slidesToShow'] === 3
         && $r['data']['responsive'][1]['settings']['slidesToShow'] === 1, 'SL-03 breakpoints 991 and 480' );
slCheck( $s( array(), 4, 9 )['data']['slidesToShow'] === 4 && $s( array(), 4, 9 )['data']['responsive'][0]['settings']['slidesToShow'] === 4, 'SL-03 per_view, and per_view_md never above it' );

// SL-04
$r = $s( array( 'number_of_thumbnails' => "2' onmouseover='x", 'transition' => "fade'><script>", 'autoplay_time' => "<b>" ) );
slCheck( strpbrk( $r['json'], "'<>&" ) === false, 'SL-04 no quote, angle bracket or ampersand in the JSON' );
slCheck( $r['data']['slidesToShow'] === 2, 'SL-04 a stored value is read as a number' );

// SL-05
$autoload = (string)file_get_contents( $root . '/autoloads/eztemplateautoload.php' );
slCheck( strpos( $autoload, "'explayouts_slider_settings'" ) !== false && strpos( $autoload, 'explayoutsslidersettings.php' ) !== false, 'SL-05 operator registered' );
$op = new expLayoutsSliderSettings();
slCheck( $op->operatorList() === array( 'explayouts_slider_settings' ), 'SL-05 operator list' );
$value = array( 'show_pagination' => '1' );
$op->modify( null, 'explayouts_slider_settings', array(), '', '', $value, array( 'per_view' => 3, 'per_view_md' => 2 ) );
slCheck( is_array( $value ) && $value['dots'] === true && json_decode( $value['json'], true )['slidesToShow'] === 3, 'SL-05 operator returns the settings' );
$part = (string)file_get_contents( $root . '/design/standard/templates/explayouts/block/parts/slick_attributes.tpl' );
slCheck( strpos( $part, 'explayouts_slider_settings' ) !== false && stripos( $part, 'style=' ) === false && stripos( $part, '<style' ) === false, 'SL-05 the part uses the operator, no inline style' );
$image = (string)file_get_contents( $root . '/design/standard/templates/explayouts/tpl_block/image.tpl' );
slCheck( strpos( $image, 'PageImageSettings' ) !== false && stripos( $image, 'style=' ) === false, 'SL-05 the page image block reads its settings, no inline style' );

// SL-06
$t = microtime( true );
for ( $i = 0; $i < 10000; $i++ )
    expLayoutsSliderSettings::settings( array( 'number_of_thumbnails' => '3', 'show_pagination' => '1', 'autoplay' => '1' ) );
$us = ( microtime( true ) - $t ) * 1e6 / 10000;
slCheck( $us < 100, sprintf( 'SL-06 %.1f microseconds per slider', $us ) );

echo $failures ? "FAIL: $failures\n" : "PASS: all\n";
exit( $failures ? 1 : 0 );
