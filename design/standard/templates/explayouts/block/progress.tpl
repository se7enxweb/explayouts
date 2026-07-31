<div class="slb slb-progress" data-block-id="{$block.id|wash}">
    {if $block.values.label}<label>{$block.values.label|wash}</label>{/if}
    <div class="progress-track">
        <div class="progress-bar" style="width:{$block.values.value|wash}%;background-color:{$block.values.color|wash}"></div>
    </div>
    <span class="progress-value">{$block.values.value|wash}%</span>
</div>
