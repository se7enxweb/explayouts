<div class="slb slb-map" data-block-id="{$block.id|wash}">
    {if $block.values.embed_url}
        <iframe src="{$block.values.embed_url|wash}" width="{$block.values.width|wash}" height="{$block.values.height|wash}" style="border:0;" allowfullscreen loading="lazy"></iframe>
    {else}
        <p class="empty">No map configured.</p>
    {/if}
</div>
