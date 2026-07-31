<div class="slb slb-gallery" data-block-id="{$block.id|wash}">
    {if count($block.values.images)}
        <div class="gallery-grid">
        {foreach $block.values.images as $image}
            {if $image.link}<a href={$image.link|ezurl}>{/if}
            <img src="{$image.url|wash}" alt="{$image.alt|wash}" />
            {if $image.link}</a>{/if}
        {/foreach}
        </div>
    {else}
        <p class="empty">No images found.</p>
    {/if}
</div>
