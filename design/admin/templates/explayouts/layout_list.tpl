<div class="context-block">
    <div class="box-header"><h1 class="context-title">Exponential Layouts</h1></div>
    <div class="box-ml">
        {if $message}<div class="message-feedback">{$message|wash}</div>{/if}
        {if $error}<div class="message-error">{$error|wash}</div>{/if}
        <table class="list" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>{'Identifier'|i18n( 'design/admin/explayouts/layout_list' )}</th>
                <th>{'Name'|i18n( 'design/admin/explayouts/layout_list' )}</th>
                <th>{'Type'|i18n( 'design/admin/explayouts/layout_list' )}</th>
                <th>{'Status'|i18n( 'design/admin/explayouts/layout_list' )}</th>
                <th>&nbsp;</th>
            </tr>
            {foreach $layouts as $layout}
                <tr>
                    <td>{$layout.id|wash}</td>
                    <td>{$layout.identifier|wash}</td>
                    <td>{$layout.name|wash}</td>
                    <td>{$layout.layout_type|wash}</td>
                    <td>{if eq($layout.status,2)}{'Published'|i18n( 'design/admin/explayouts/layout_list' )}{else}{'Draft'|i18n( 'design/admin/explayouts/layout_list' )}{/if}</td>
                    <td>
                        <a href={concat('explayouts/layout_edit/',$layout.id)|ezurl} title="{'Edit'|i18n( 'design/admin/explayouts/layout_list' )}"><img src={'edit.gif'|ezimage} width="16" height="16" alt="{'Edit'|i18n( 'design/admin/explayouts/layout_list' )}" style="vertical-align:middle;" /></a>
                        <a href={concat('explayouts/layout_preview/',$layout.id,'/',$layout.status)|ezurl} target="_blank" title="{'Preview'|i18n( 'design/admin/explayouts/layout_list' )}"><img src={'find.png'|ezimage} width="16" height="16" alt="{'Preview'|i18n( 'design/admin/explayouts/layout_list' )}" style="vertical-align:middle;" /></a>
                        {if eq($layout.status,2)}<a href="/explayouts_ui_api/app#layout/{$layout.id}/create_new_draft">{'New draft'|i18n( 'design/admin/explayouts/layout_list' )}</a>{else}<a href="/explayouts_ui_api/app#layout/{$layout.id}/edit">{'Edit in UI'|i18n( 'design/admin/explayouts/layout_list' )}</a>{/if}
                        <form method="post" action={'explayouts/layout_list'|ezurl} style="display:inline;margin:0;">
                            <input type="hidden" name="ExportLayoutID" value="{$layout.id|wash}" />
                            <button type="submit" name="ExportLayout" class="button">{'Export'|i18n( 'design/admin/explayouts/layout_list' )}</button>
                        </form>
                        <form method="post" action={'explayouts/layout_list'|ezurl} style="display:inline;margin:0;">
                            <input type="hidden" name="CopyLayoutID" value="{$layout.id|wash}" />
                            <button type="submit" name="CopyLayout" class="button">{'Copy'|i18n( 'design/admin/explayouts/layout_list' )}</button>
                        </form>
                        <form method="post" action={'explayouts/layout_list'|ezurl} style="display:inline;margin:0;">
                            <input type="hidden" name="DeleteLayoutID" value="{$layout.id|wash}" />
                            <button type="submit" name="DeleteLayout" class="button" onclick="return confirm('{'Delete this layout and all its zones/blocks?'|i18n( 'design/admin/explayouts/layout_list' )|wash( javascript )}');">{'Delete'|i18n( 'design/admin/explayouts/layout_list' )}</button>
                        </form>
                    </td>
                </tr>
            {/foreach}
        </table>

        <form method="post" action={'explayouts/layout_list'|ezurl} style="margin-top:1rem;">
            <label for="import-json"><strong>{'Import layout from JSON:'|i18n( 'design/admin/explayouts/layout_list' )}</strong></label><br/>
            <textarea id="import-json" name="ImportJson" rows="6" cols="80" placeholder="{'Paste exported layout JSON here'|i18n( 'design/admin/explayouts/layout_list' )}"></textarea><br/>
            <button type="submit" name="ImportLayout" class="defaultbutton">{'Import layout'|i18n( 'design/admin/explayouts/layout_list' )}</button>
        </form>

        <div class="controlbar">
            <a class="button" href={'explayouts/layout_edit/'|ezurl}>{'New layout'|i18n( 'design/admin/explayouts/layout_list' )}</a>
            <a class="button" href={'explayouts/template_editor/'|ezurl}>{'Template editor'|i18n( 'design/admin/explayouts/layout_list' )}</a>
            <a class="button" href={'explayouts/rule_list'|ezurl}>{'Rules'|i18n( 'design/admin/explayouts/layout_list' )}</a>
            <a class="button" href={'explayouts/setup'|ezurl}>{'Setup DB'|i18n( 'design/admin/explayouts/layout_list' )}</a>
        </div>
    </div>
</div>
