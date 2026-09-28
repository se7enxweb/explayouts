<div class="context-block">
    <div class="box-header"><h1 class="context-title">{'Layout Rules'|i18n( 'design/admin/explayouts/rule_list' )}</h1></div>
    <div class="box-ml">
        {if $message}<div class="message-feedback">{$message|wash}</div>{/if}
        {if $error}<div class="message-error">{$error|wash}</div>{/if}
        <table class="list" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>{'Priority'|i18n( 'design/admin/explayouts/rule_list' )}</th>
                <th>{'Enabled'|i18n( 'design/admin/explayouts/rule_list' )}</th>
                <th>{'Layout ID'|i18n( 'design/admin/explayouts/rule_list' )}</th>
                <th>&nbsp;</th>
            </tr>
            {foreach $rules as $rule}
                <tr>
                    <td>{$rule.id|wash}</td>
                    <td>{$rule.priority|wash}</td>
                    <td>{if $rule.enabled}{'Yes'|i18n( 'design/admin/explayouts/rule_list' )}{else}{'No'|i18n( 'design/admin/explayouts/rule_list' )}{/if}</td>
                    <td>{$rule.layout_id|wash}</td>
                    <td>
                        <a href={concat('explayouts/rule_edit/',$rule.id)|ezurl} title="{'Edit'|i18n( 'design/admin/explayouts/rule_list' )}"><img src={'edit.gif'|ezimage} width="16" height="16" alt="{'Edit'|i18n( 'design/admin/explayouts/rule_list' )}" style="vertical-align:middle;" /></a>
                        <form method="post" action={'explayouts/rule_list'|ezurl} style="display:inline;margin:0;">
                            <input type="hidden" name="CopyRuleID" value="{$rule.id|wash}" />
                            <button type="submit" name="CopyRule" class="button">{'Copy'|i18n( 'design/admin/explayouts/rule_list' )}</button>
                        </form>
                        <form method="post" action={'explayouts/rule_list'|ezurl} style="display:inline;margin:0;">
                            <input type="hidden" name="DeleteRuleID" value="{$rule.id|wash}" />
                            <button type="submit" name="DeleteRule" class="button" onclick="return confirm('{'Delete this rule?'|i18n( 'design/admin/explayouts/rule_list' )|wash( javascript )}');">{'Delete'|i18n( 'design/admin/explayouts/rule_list' )}</button>
                        </form>
                    </td>
                </tr>
            {/foreach}
        </table>
        <a class="button" href={'explayouts/rule_edit/'|ezurl}><img src={'new.png'|ezimage} width="16" height="16" alt="{'New rule'|i18n( 'design/admin/explayouts/rule_list' )}" style="vertical-align:middle;" /> {'New rule'|i18n( 'design/admin/explayouts/rule_list' )}</a>
    </div>
</div>
