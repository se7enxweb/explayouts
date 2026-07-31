<div class="slb slb-list" data-block-id="{$block.id|wash}">
    {if $block.name}<h3>{$block.name|wash}</h3>{/if}
    <ul class="block-list">
    {foreach $block.values.items as $node}
        <li><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></li>
    {/foreach}
    {if eq(count($block.values.items),0)}
        <li class="empty">No items found.</li>
    {/if}
    </ul>
</div>
