<blockquote class="slb slb-quote quote-{$block.values.align|wash}" data-block-id="{$block.id|wash}">
    <p class="quote-text">{$block.values.quote|wash}</p>
    {if $block.values.author}<footer>{$block.values.author|wash}{if $block.values.source}, <cite>{$block.values.source|wash}</cite>{/if}</footer>{/if}
</blockquote>
