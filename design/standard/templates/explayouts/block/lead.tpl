<section class="slb slb-lead{if $block.values.css_class} {$block.values.css_class}{/if}" {if $block.values.css_id}id="{$block.values.css_id|wash}"{/if} data-block-id="{$block.id|wash}">
    {if $block.values.title}<h2>{$block.values.title|wash}</h2>{/if}
    {if $block.values.content}<p class="lead">{$block.values.content|nl2br}</p>{/if}
    {if and( $block.values.link_text, $block.values.link_url )}<a href="{$block.values.link_url|ezurl}" class="btn btn-primary">{$block.values.link_text|wash}</a>{/if}
</section>
