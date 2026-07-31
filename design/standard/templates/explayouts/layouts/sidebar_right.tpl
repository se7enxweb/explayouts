<div class="slb slb-sidebar-right" data-layout-id="{$layout.id|wash}">
    {foreach $layout.zones as $zone}
        {if eq($zone.identifier,'main')}
            <main class="sevenx-zone sevenx-zone-main" data-zone-id="{$zone.id|wash}">
                {foreach $zone.blocks as $block}
                    {include uri=concat('design:explayouts/block/',$block.definition_identifier,'.tpl') block=$block}
                {/foreach}
            </main>
        {/if}
        {if eq($zone.identifier,'sidebar')}
            <aside class="sevenx-zone sevenx-zone-sidebar" data-zone-id="{$zone.id|wash}">
                {foreach $zone.blocks as $block}
                    {include uri=concat('design:explayouts/block/',$block.definition_identifier,'.tpl') block=$block}
                {/foreach}
            </aside>
        {/if}
    {/foreach}
</div>
