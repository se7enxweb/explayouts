<div class="slb slb-4-column" data-layout-id="{$layout.id|wash}">
    {foreach $layout.zones as $zone}
        {if or(eq($zone.identifier,'col1'),eq($zone.identifier,'col2'),eq($zone.identifier,'col3'),eq($zone.identifier,'col4'))}
            <div class="sevenx-zone sevenx-zone-{$zone.identifier|wash}" data-zone-id="{$zone.id|wash}">
                {foreach $zone.blocks as $block}
                    {include uri=concat('design:explayouts/block/',$block.definition_identifier,'.tpl') block=$block}
                {/foreach}
            </div>
        {/if}
    {/foreach}
</div>
