<div class="slb slb-featured" data-layout-id="{$layout.id|wash}">
    {foreach $layout.zones as $zone}
        {if eq($zone.identifier,'hero')}
            <div class="sevenx-zone sevenx-zone-hero" data-zone-id="{$zone.id|wash}">
                {foreach $zone.blocks as $block}
                    {include uri=concat('design:explayouts/block/',$block.definition_identifier,'.tpl') block=$block}
                {/foreach}
            </div>
        {/if}
    {/foreach}
    <div class="slb-featured-row">
        {foreach $layout.zones as $zone}
            {if or(eq($zone.identifier,'feature1'),eq($zone.identifier,'feature2'),eq($zone.identifier,'feature3'))}
                <div class="sevenx-zone sevenx-zone-{$zone.identifier|wash}" data-zone-id="{$zone.id|wash}">
                    {foreach $zone.blocks as $block}
                        {include uri=concat('design:explayouts/block/',$block.definition_identifier,'.tpl') block=$block}
                    {/foreach}
                </div>
            {/if}
        {/foreach}
    </div>
    {foreach $layout.zones as $zone}
        {if eq($zone.identifier,'bottom')}
            <div class="sevenx-zone sevenx-zone-bottom" data-zone-id="{$zone.id|wash}">
                {foreach $zone.blocks as $block}
                    {include uri=concat('design:explayouts/block/',$block.definition_identifier,'.tpl') block=$block}
                {/foreach}
            </div>
        {/if}
    {/foreach}
</div>
