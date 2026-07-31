<div class="slb slb-video" data-block-id="{$block.id|wash}">
    {if $block.values.video_url}
        <iframe src="{$block.values.video_url|wash}" width="{$block.values.width|wash}" height="{$block.values.height|wash}" frameborder="0" allowfullscreen
                {if $block.values.autoplay}allow="autoplay"{/if}></iframe>
    {else}
        <p class="empty">No video configured.</p>
    {/if}
</div>
