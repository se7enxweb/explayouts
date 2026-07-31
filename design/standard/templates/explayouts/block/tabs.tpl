<div class="slb slb-tabs" data-block-id="{$block.id|wash}">
    <div class="tab-buttons">
    {foreach $block.values.items as $idx => $item}
        <button class="tab-button{if $idx|eq($block.values.active_index)} active{/if}">{$item.title|wash}</button>
    {/foreach}
    </div>
    <div class="tab-panels">
    {foreach $block.values.items as $idx => $item}
        <div class="tab-panel{if $idx|eq($block.values.active_index)} active{/if}">{$item.content}</div>
    {/foreach}
    </div>
    {if eq(count($block.values.items),0)}<p class="empty">No tabs.</p>{/if}
</div>
