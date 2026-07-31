<div class="slb slb-card" data-block-id="{$block.id|wash}">
    {if $block.values.image_url}<img src="{$block.values.image_url|wash}" alt="{$block.values.title|wash}" />{/if}
    {if $block.values.title}<h3>{$block.values.title|wash}</h3>{/if}
    {if $block.values.content}<p>{$block.values.content|wash}</p>{/if}
    {if $block.values.link_url}<a href="{$block.values.link_url|wash}">{$block.values.link_label|wash}</a>{/if}
</div>
