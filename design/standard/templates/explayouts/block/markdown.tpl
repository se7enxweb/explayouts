<div class="slb slb-markdown" data-block-id="{$block.id|wash}">
    {if $block.name}<h3>{$block.name|wash}</h3>{/if}
    <div class="block-content">
        {if is_set($block.values.html)}{$block.values.html}{/if}
    </div>
</div>
