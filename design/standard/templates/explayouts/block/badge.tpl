<div class="slb slb-badge" data-block-id="{$block.id|wash}">
    {if $block.values.link}<a href="{$block.values.link|wash}" class="badge badge-{$block.values.style|wash}">{else}<span class="badge badge-{$block.values.style|wash}">{/if}
        {$block.values.label|wash}
    {if $block.values.link}</a>{else}</span>{/if}
</div>
