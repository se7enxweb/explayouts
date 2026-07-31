<div class="slb slb-button" data-block-id="{$block.id|wash}">
    {if $block.values.text|ne('') and $block.values.link|ne('')}
        <a href={$block.values.link|ezurl} target="{$block.values.target|wash}" class="{$block.values.class|wash}">{$block.values.text|wash}</a>
    {elseif $block.values.text|ne('')}
        <span class="{$block.values.class|wash}">{$block.values.text|wash}</span>
    {/if}
</div>