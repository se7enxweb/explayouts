<div class="slb slb-mosaic" data-layout-id="{$layout.id|wash}">
    {foreach $layout.zones as $zone}
        {if or(eq($zone.identifier,'a'),eq($zone.identifier,'b'),eq($zone.identifier,'c'),eq($zone.identifier,'d'),eq($zone.identifier,'e'))}
            <div class="sevenx-zone sevenx-zone-{$zone.identifier|wash}" data-zone-id="{$zone.id|wash}">
                {foreach $zone.blocks as $block}
                    {include uri=concat('design:explayouts/block/',$block.definition_identifier,'.tpl') block=$block}
                {/foreach}
            </div>
        {/if}
    {/foreach}
</div>
