<div class="slb slb-title" data-block-id="{$block.id|wash}">
    {if $block.values.title}
        {if $block.values.tag|ne('')}
            {def $tag = $block.values.tag}
        {else}
            {def $tag = concat('h',$block.values.level)}
        {/if}
        <{$tag} class="title">
            {if and($block.values.use_link|eq(1), $block.values.link|ne(''))}
                <a href={$block.values.link|ezurl}>{$block.values.title|wash}</a>
            {else}
                {$block.values.title|wash}
            {/if}
        </{$tag}>
        {undef $tag}
    {/if}
</div>