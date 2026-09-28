<div class="context-block">
    <div class="box-header"><h1 class="context-title">{'Exponential Layouts Dashboard'|i18n( 'design/admin/explayouts/dashboard' )}</h1></div>
    <div class="box-ml">
        <table class="list" cellspacing="0">
            <tr>
                <th>{'Resource'|i18n( 'design/admin/explayouts/dashboard' )}</th>
                <th>{'Count'|i18n( 'design/admin/explayouts/dashboard' )}</th>
            </tr>
            <tr>
                <td>{'Layouts'|i18n( 'design/admin/explayouts/dashboard' )}</td>
                <td>{$counts.layouts|wash}</td>
            </tr>
            <tr>
                <td>{'Zones'|i18n( 'design/admin/explayouts/dashboard' )}</td>
                <td>{$counts.zones|wash}</td>
            </tr>
            <tr>
                <td>{'Blocks'|i18n( 'design/admin/explayouts/dashboard' )}</td>
                <td>{$counts.blocks|wash}</td>
            </tr>
            <tr>
                <td>{'Rules'|i18n( 'design/admin/explayouts/dashboard' )}</td>
                <td>{$counts.rules|wash}</td>
            </tr>
            <tr>
                <td>{'Collections'|i18n( 'design/admin/explayouts/dashboard' )}</td>
                <td>{$counts.collections|wash}</td>
            </tr>
        </table>

        <h3>{'Recent layouts'|i18n( 'design/admin/explayouts/dashboard' )}</h3>
        {if count($recent_layouts)}
            <table class="list" cellspacing="0">
                <tr><th>ID</th><th>{'Name'|i18n( 'design/admin/explayouts/dashboard' )}</th><th>{'Identifier'|i18n( 'design/admin/explayouts/dashboard' )}</th><th>{'Modified'|i18n( 'design/admin/explayouts/dashboard' )}</th></tr>
                {foreach $recent_layouts as $layout}
                    <tr>
                        <td>{$layout.id|wash}</td>
                        <td><a href={concat('explayouts/layout_edit/',$layout.id)|ezurl}>{$layout.name|wash}</a></td>
                        <td>{$layout.identifier|wash}</td>
                        <td>{$layout.modified|datetime( 'custom', '%Y-%m-%d %H:%M' )}</td>
                    </tr>
                {/foreach}
            </table>
        {else}
            <p>{'No layouts yet.'|i18n( 'design/admin/explayouts/dashboard' )}</p>
        {/if}

        <div class="controlbar">
            <a class="button" href={'explayouts/layout_list'|ezurl}>{'Layouts'|i18n( 'design/admin/explayouts/dashboard' )}</a>
            <a class="button" href={'explayouts/rule_list'|ezurl}>{'Rules'|i18n( 'design/admin/explayouts/dashboard' )}</a>
            <a class="button" href={'explayouts/template_editor/'|ezurl}>{'Template editor'|i18n( 'design/admin/explayouts/dashboard' )}</a>
            <a class="button" href={'explayouts/setup'|ezurl}>{'Setup DB'|i18n( 'design/admin/explayouts/dashboard' )}</a>
        </div>
    </div>
</div>
