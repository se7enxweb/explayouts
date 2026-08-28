<div class="slb slb-tpl-block" data-block-id="{$block.id|wash}" data-tpl-block-name="{if is_set($block.values.block_name)}{$block.values.block_name|wash}{/if}">
    {if and( is_set($block.values.block_name), ne($block.values.block_name, '') )}
        {include uri=concat('design:explayouts/tpl_block/', $block.values.block_name, '.tpl')}
    {/if}
</div>
