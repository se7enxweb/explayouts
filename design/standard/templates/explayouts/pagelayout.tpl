<!DOCTYPE html>
<html lang="{ezini('RegionalSettings','Locale')}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{if is_set($module_result.title)}{$module_result.title|wash}{else}Exponential Layouts{/if}</title>
    <link rel="stylesheet" type="text/css" href={'stylesheets/explayouts_standard/grid.css'|ezdesign} />
    <link rel="stylesheet" type="text/css" href={'stylesheets/explayouts.css'|ezdesign} />
    <link rel="stylesheet" type="text/css" href={'stylesheets/explayouts_standard/style-full.css'|ezdesign} />
    {if is_set($module_result.css_files)}{foreach $module_result.css_files as $css}<link rel="stylesheet" type="text/css" href={$css|ezurl} />{/foreach}{/if}
    {if is_set($module_result.js_files)}{foreach $module_result.js_files as $js}<script type="text/javascript" src={$js|ezurl}></script>{/foreach}{/if}
</head>
<body>
{def $sevenx_layout = fetch('explayouts','resolve_layout',hash())}
{if $sevenx_layout}
    {include uri='design:explayouts/layout.tpl' layout=$sevenx_layout}
{else}
    {$module_result.content}
{/if}
</body>
</html>
