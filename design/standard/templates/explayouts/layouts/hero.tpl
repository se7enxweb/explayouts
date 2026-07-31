<div class="slb slb-hero" data-layout-id="{$layout.id|wash}">
    {foreach $layout.zones as $zone}
        {if eq($zone.identifier,'top')}
            <div class="sevenx-zone sevenx-zone-top" data-zone-id="{$zone.id|wash}">
                {foreach $zone.blocks as $block}
                    {include uri=concat('design:explayouts/block/',$block.definition_identifier,'.tpl') block=$block}
                {/foreach}
            </div>
        {/if}
    {/foreach}
    <div class="slb-hero-row">
        {foreach $layout.zones as $zone}
            {if or(eq($zone.identifier,'left'),eq($zone.identifier,'main'),eq($zone.identifier,'right'))}
                <div class="sevenx-zone sevenx-zone-{$zone.identifier|wash}" data-zone-id="{$zone.id|wash}">
                    {foreach $zone.blocks as $block}
                        {include uri=concat('design:explayouts/block/',$block.definition_identifier,'.tpl') block=$block}
                    {/foreach}
                </div>
            {/if}
        {/foreach}
    </div>
</div>
