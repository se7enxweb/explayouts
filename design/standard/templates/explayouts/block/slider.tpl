<div class="slb slb-slider" data-block-id="{$block.id|wash}">
    {if count($block.values.images)}
        <div class="slider">
        {foreach $block.values.images as $image}
            <div class="slide">
                {if $image.link}<a href={$image.link|ezurl}>{/if}
                <img src="{$image.url|wash}" alt="{$image.alt|wash}" />
                {if $image.link}</a>{/if}
            </div>
        {/foreach}
        </div>
    {else}
        <p class="empty">No images found.</p>
    {/if}
</div>
