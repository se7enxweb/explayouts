<div class="context-block">
    <div class="box-header"><h1 class="context-title">Layout Rules</h1></div>
    <div class="box-ml">
        {if $message}<div class="message-feedback">{$message|wash}</div>{/if}
        {if $error}<div class="message-error">{$error|wash}</div>{/if}
        <table class="list" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Priority</th>
                <th>Enabled</th>
                <th>Layout ID</th>
                <th>&nbsp;</th>
            </tr>
            {foreach $rules as $rule}
                <tr>
                    <td>{$rule.id|wash}</td>
                    <td>{$rule.priority|wash}</td>
                    <td>{if $rule.enabled}Yes{else}No{/if}</td>
                    <td>{$rule.layout_id|wash}</td>
                    <td>
                        <a href={concat('explayouts/rule_edit/',$rule.id)|ezurl} title="Edit"><img src={'edit.gif'|ezimage} width="16" height="16" alt="Edit" style="vertical-align:middle;" /></a>
                        <form method="post" action={'explayouts/rule_list'|ezurl} style="display:inline;margin:0;">
                            <input type="hidden" name="CopyRuleID" value="{$rule.id|wash}" />
                            <button type="submit" name="CopyRule" class="button">Copy</button>
                        </form>
                        <form method="post" action={'explayouts/rule_list'|ezurl} style="display:inline;margin:0;">
                            <input type="hidden" name="DeleteRuleID" value="{$rule.id|wash}" />
                            <button type="submit" name="DeleteRule" class="button" onclick="return confirm('Delete this rule?');">Delete</button>
                        </form>
                    </td>
                </tr>
            {/foreach}
        </table>
        <a class="button" href={'explayouts/rule_edit/'|ezurl}><img src={'new.png'|ezimage} width="16" height="16" alt="New rule" style="vertical-align:middle;" /> New rule</a>
    </div>
</div>
