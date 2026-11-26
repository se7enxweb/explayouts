{* The attributes of a slick slider that follows the block's settings in the Design tab, for the slider of any design:

     <div{include uri='design:explayouts/block/parts/slick_attributes.tpl' block=$block}>
         ... one element per slide ...
     </div>

   Writes class="slick-slider[ has-dots][ <class>]" and data-slick='{...}' (slick's own settings attribute), so a
   design needs no script of its own and no inline style. Thumbnails per row/slide, next and previous buttons,
   pagination, infinite loop, fade and autoplay with its time: expLayoutsSliderSettings (the operator
   explayouts_slider_settings) maps them, in PHP.
   Input: block; optional class (more classes), per_view (items per slide when the block stores none, default 2),
   per_view_md (the most items per slide up to 991px wide, default 2). *}
{def $sa = first_set( $block.parameters, hash() )|explayouts_slider_settings( first_set( $per_view, 2 ), first_set( $per_view_md, 2 ) )} class="{$sa.class}{if first_set( $class, '' )|ne( '' )} {$class|wash}{/if}" data-slick='{$sa.json}'{undef $sa}
