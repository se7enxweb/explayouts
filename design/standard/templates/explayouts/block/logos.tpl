<div class="slb slb-logos" data-block-id="{$block.id|wash}">
    {if $block.name}<h3>{$block.name|wash}</h3>{/if}
    <div class="logos-row">
    {foreach $block.values.items as $item}
        {if $item.has_image}
        <div class="logo-item">
            <a href="{$item.link|ezurl}"><img src="{$item.url|wash}" alt="{$item.alt|wash}" /></a>
        </div>
        {/if}
    {/foreach}
    {if eq(count($block.values.items),0)}
        <p class="empty">No logos found.</p>
    {/if}
    </div>
</div>
