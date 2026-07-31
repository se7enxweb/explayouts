<div class="slb slb-accordion" data-block-id="{$block.id|wash}">
    {foreach $block.values.items as $idx => $item}
        <details {if and($block.values.open_first,$idx|eq(0))}open="open"{/if}>
            <summary>{$item.title|wash}</summary>
            <div class="accordion-content">{$item.content}</div>
        </details>
    {/foreach}
    {if eq(count($block.values.items),0)}<p class="empty">No accordion items.</p>{/if}
</div>
