<div class="context-block">
    <div class="box-header"><h1 class="context-title">Exponential Layouts</h1></div>
    <div class="box-ml">
        {if $message}<div class="message-feedback">{$message|wash}</div>{/if}
        {if $error}<div class="message-error">{$error|wash}</div>{/if}
        <table class="list" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Identifier</th>
                <th>Name</th>
                <th>Type</th>
                <th>Status</th>
                <th>&nbsp;</th>
            </tr>
            {foreach $layouts as $layout}
                <tr>
                    <td>{$layout.id|wash}</td>
                    <td>{$layout.identifier|wash}</td>
                    <td>{$layout.name|wash}</td>
                    <td>{$layout.layout_type|wash}</td>
                    <td>{if eq($layout.status,2)}Published{else}Draft{/if}</td>
                    <td>
                        <a href={concat('explayouts/layout_edit/',$layout.id)|ezurl} title="Edit"><img src={'edit.gif'|ezimage} width="16" height="16" alt="Edit" style="vertical-align:middle;" /></a>
                        <a href={concat('explayouts/layout_preview/',$layout.id,'/',$layout.status)|ezurl} target="_blank" title="Preview"><img src={'find.png'|ezimage} width="16" height="16" alt="Preview" style="vertical-align:middle;" /></a>
                        {if eq($layout.status,2)}<a href="/explayouts_ui_api/app#layout/{$layout.id}/create_new_draft">New draft</a>{else}<a href="/explayouts_ui_api/app#layout/{$layout.id}/edit">Edit in UI</a>{/if}
                        <form method="post" action={'explayouts/layout_list'|ezurl} style="display:inline;margin:0;">
                            <input type="hidden" name="ExportLayoutID" value="{$layout.id|wash}" />
                            <button type="submit" name="ExportLayout" class="button">Export</button>
                        </form>
                        <form method="post" action={'explayouts/layout_list'|ezurl} style="display:inline;margin:0;">
                            <input type="hidden" name="CopyLayoutID" value="{$layout.id|wash}" />
                            <button type="submit" name="CopyLayout" class="button">Copy</button>
                        </form>
                        <form method="post" action={'explayouts/layout_list'|ezurl} style="display:inline;margin:0;">
                            <input type="hidden" name="DeleteLayoutID" value="{$layout.id|wash}" />
                            <button type="submit" name="DeleteLayout" class="button" onclick="return confirm('Delete this layout and all its zones/blocks?');">Delete</button>
                        </form>
                    </td>
                </tr>
            {/foreach}
        </table>

        <form method="post" action={'explayouts/layout_list'|ezurl} style="margin-top:1rem;">
            <label for="import-json"><strong>Import layout from JSON:</strong></label><br/>
            <textarea id="import-json" name="ImportJson" rows="6" cols="80" placeholder="Paste exported layout JSON here"></textarea><br/>
            <button type="submit" name="ImportLayout" class="defaultbutton">Import layout</button>
        </form>

        <div class="controlbar">
            <a class="button" href={'explayouts/layout_edit/'|ezurl}>New layout</a>
            <a class="button" href={'explayouts/template_editor/'|ezurl}>Template editor</a>
            <a class="button" href={'explayouts/rule_list'|ezurl}>Rules</a>
            <a class="button" href={'explayouts/setup'|ezurl}>Setup DB</a>
        </div>
    </div>
</div>
