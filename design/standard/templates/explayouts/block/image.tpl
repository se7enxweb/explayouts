<div class="slb slb-image" data-block-id="{$block.id|wash}">
    {if $block.values.image_url}
        {if $block.values.link}<a href="{$block.values.link|wash}" target="_blank">{/if}
        <img src="{$block.values.image_url|wash}" alt="{$block.values.alt|wash}" />
        {if $block.values.link}</a>{/if}
    {/if}
</div>
