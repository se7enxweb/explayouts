<div class="slb slb-list-accordion" data-block-id="{$block.id|wash}">
    {if $block.name}<h3>{$block.name|wash}</h3>{/if}
    {if count($block.values.items)}
        <div class="block-accordion">
        {foreach $block.values.items as $node}
            <details class="accordion-item">
                <summary>{$node.name|wash}</summary>
                <p><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></p>
            </details>
        {/foreach}
        </div>
    {else}
        <p class="empty">No items found.</p>
    {/if}
</div>
