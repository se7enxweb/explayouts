<div class="slb" data-layout-id="{$layout.id|wash}" data-layout-identifier="{$layout.identifier|wash}">
{foreach $layout.zones as $zone}
    {include uri='design:explayouts/zone.tpl' zone=$zone module_result=$module_result}
{/foreach}
</div>
