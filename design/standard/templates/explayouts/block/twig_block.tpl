<div class="slb slb-twig-block" data-block-id="{$block.id|wash}" data-twig-block-name="{if is_set($block.values.block_name)}{$block.values.block_name|wash}{/if}">
    {if and( is_set($block.values.block_name), ne($block.values.block_name, '') )}
        {include uri=concat('design:explayouts/twig_block/', $block.values.block_name, '.tpl')}
    {/if}
</div>
