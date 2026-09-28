<div class="slb slb-alert alert alert-{$block.values.type|wash}" data-block-id="{$block.id|wash}">
    {$block.values.message|wash}
    {if $block.values.dismissible}<button class="alert-close" aria-label="{'Close'|i18n( 'design/standard/explayouts/block/alert' )}">x</button>{/if}
</div>
