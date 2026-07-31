<div class="slb slb-carousel" data-block-id="{$block.id|wash}" data-autoplay="{$block.values.autoplay|wash}" data-slides="{$block.values.slides_per_view|wash}">
    {if count($block.values.slides)}
        <div class="carousel-viewport">
            {foreach $block.values.slides as $slide}
                <div class="carousel-slide">
                    {if $slide.image_url}<img src="{$slide.image_url|wash}" alt="{$slide.node.name|wash}" />{/if}
                    {if $slide.node}<a href={$slide.node.url_alias|ezurl}>{$slide.node.name|wash}</a>{/if}
                </div>
            {/foreach}
        </div>
    {else}
        <p class="empty">No carousel slides found.</p>
    {/if}
</div>
