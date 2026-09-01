<!DOCTYPE html>
<html lang="{ezini('RegionalSettings','Locale')}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2" />
    <title>{if is_set($module_result.title)}{$module_result.title|wash}{else}Exponential Layouts{/if}</title>
    {if is_set($module_result.css_files)}{foreach $module_result.css_files as $css}<link rel="stylesheet" type="text/css" href={$css|ezurl} />{/foreach}{/if}
    {if is_set($module_result.js_files)}{foreach $module_result.js_files as $js}<script type="text/javascript" src={$js|ezurl}></script>{/foreach}{/if}
</head>
<body>
{def $sevenx_layout = false()}
{if and( is_set( $module_result.content_info.node_id ), $module_result.content_info.node_id|gt( 0 ) )}
    {set $sevenx_layout = fetch( 'explayouts', 'resolve_layout_for_node', hash( 'node_id', $module_result.content_info.node_id ) )}
{else}
    {set $sevenx_layout = fetch( 'explayouts', 'resolve_layout', hash() )}
{/if}
<main class="main-content-block">
    <section class="zone zone-post_header"></section>
    <section class="zone zone-main">
{if $sevenx_layout}
    {include uri='design:explayouts/layout.tpl' layout=$sevenx_layout module_result=$module_result}
{else}
    {$module_result.content}
{/if}
    </section>
</main>
</body>
</html>
