<div class="slb slb-grid" data-block-id="{$block.id|wash}">
    {if $block.name}<h3>{$block.name|wash}</h3>{/if}
    {def $columns = $block.values.columns}
    {if $columns|eq(2)}{def $col_class = 'col-sm-6 col-md-6 col-lg-6'}{/if}
    {if $columns|eq(3)}{def $col_class = 'col-sm-6 col-md-6 col-lg-4'}{/if}
    {if $columns|eq(4)}{def $col_class = 'col-sm-6 col-md-4 col-lg-3'}{/if}
    {if $columns|eq(6)}{def $col_class = 'col-sm-4 col-md-3 col-lg-2'}{/if}
    <div class="row">
    {foreach $block.values.items as $node}
        <div class="{$col_class}">
            <div class="grid-item">
                <a href={$node.url_alias|ezurl}>{$node.name|wash}</a>
            </div>
        </div>
    {/foreach}
    </div>
    {if eq(count($block.values.items),0)}<p class="empty">No items found.</p>{/if}
    {undef $columns $col_class}
</div>