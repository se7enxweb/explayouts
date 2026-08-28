<div class="slb slb-list-zigzag" data-block-id="{$block.id|wash}">
    {if $block.name}<h3>{$block.name|wash}</h3>{/if}
    {if count($block.values.items)}
        <ul class="block-list-zigzag">
        {foreach $block.values.items as $node}
            <li class="zigzag-item"><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></li>
        {/foreach}
        </ul>
    {else}
        <p class="empty">No items found.</p>
    {/if}
</div>
