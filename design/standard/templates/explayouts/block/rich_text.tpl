<div class="slb slb-rich-text" data-block-id="{$block.id|wash}">
    {if $block.name}<h3>{$block.name|wash}</h3>{/if}
    <div class="block-content">
        {if is_set($block.values.content)}{$block.values.content}{/if}
    </div>
</div>
