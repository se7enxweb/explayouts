<div class="ngl-block ngl-column ngl-vt-column whitespace-top-medium whitespace-bottom-medium bg-color-secondary" data-block-id="{$block.id|wash}">
    <div class="container">
    {foreach $zone.blocks as $child}
        {if and( is_set( $child.parent_id ), $child.parent_id|eq($block.id), is_set( $child.placeholder ), $child.placeholder|eq('main'), is_set( $child.definition_identifier ), $child.definition_identifier|ne('') )}
            {include uri=concat('design:explayouts/block/', $child.definition_identifier, '.tpl') block=$child zone=$zone module_result=$module_result}
        {/if}
    {/foreach}
    </div>
</div>
