{def $left_col = cond($block.view_type|eq('two_columns_33_66'), 'col-md-4', cond($block.view_type|eq('two_columns_66_33'), 'col-md-8', 'col-md-6'))}
{def $right_col = cond($block.view_type|eq('two_columns_33_66'), 'col-md-8', cond($block.view_type|eq('two_columns_66_33'), 'col-md-4', 'col-md-6'))}

<div class="ngl-block ngl-two-columns whitespace-top-medium whitespace-bottom-medium" data-block-id="{$block.id|wash}">
    <div class="container">
        <div class="row">
            <div class="{$left_col}">
                {foreach $zone.blocks as $child}
                    {if and( is_set( $child.parent_id ), $child.parent_id|eq($block.id), is_set( $child.placeholder ), $child.placeholder|eq('left'), is_set( $child.definition_identifier ), $child.definition_identifier|ne('') )}
                        {include uri=concat('design:explayouts/block/', $child.definition_identifier, '.tpl') block=$child zone=$zone module_result=$module_result}
                    {/if}
                {/foreach}
            </div>
            <div class="{$right_col}">
                {foreach $zone.blocks as $child}
                    {if and( is_set( $child.parent_id ), $child.parent_id|eq($block.id), is_set( $child.placeholder ), $child.placeholder|eq('right'), is_set( $child.definition_identifier ), $child.definition_identifier|ne('') )}
                        {include uri=concat('design:explayouts/block/', $child.definition_identifier, '.tpl') block=$child zone=$zone module_result=$module_result}
                    {/if}
                {/foreach}
            </div>
        </div>
    </div>
</div>

{undef $left_col $right_col}
