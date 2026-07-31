<div class="slb slb-single" data-block-id="{$block.id|wash}">
    {if $block.name}<h3>{$block.name|wash}</h3>{/if}
    {if $block.values.name}
        <h4><a href={$block.values.link|ezurl}>{$block.values.name|wash}</a></h4>
        {if $block.values.intro}<p>{$block.values.intro|wash|shorten(200)}</p>{/if}
    {else}
        <p class="empty">No content selected.</p>
    {/if}
</div>