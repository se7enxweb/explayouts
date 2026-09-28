<div class="context-block">
    <div class="box-header"><h1 class="context-title">{'Edit Rule'|i18n( 'design/admin/explayouts/rule_edit' )}</h1></div>
    <div class="box-ml">
        {if $message}<div class="message-feedback">{$message|wash}</div>{/if}
        {if $error}<div class="message-error">{$error|wash}</div>{/if}

        <form method="post" action={concat('explayouts/rule_edit/',$rule.id)|ezurl}>
            <label>{'Layout:'|i18n( 'design/admin/explayouts/rule_edit' )}</label>
            <select name="LayoutID">
                {foreach $layouts as $layout}
                    <option value="{$layout.id|wash}" {if eq($rule.layout_id,$layout.id)}selected="selected"{/if}>{$layout.name|wash} ({$layout.identifier|wash})</option>
                {/foreach}
            </select><br/><br/>

            <label>{'Priority:'|i18n( 'design/admin/explayouts/rule_edit' )}</label>
            <input type="text" name="Priority" value="{$rule.priority|wash}" size="10" /><br/><br/>

            <label>
                <input type="checkbox" name="Enabled" value="1" {if $rule.enabled}checked="checked"{/if} /> {'Enabled'|i18n( 'design/admin/explayouts/rule_edit' )}
            </label><br/><br/>

            <h3>{'Targets'|i18n( 'design/admin/explayouts/rule_edit' )}</h3>
            <p>{'First matching target wins. Types: <code>path_prefix</code>, <code>path</code>, <code>path_regex</code> (pattern without delimiters), <code>node</code> (node ID or URL alias).'|i18n( 'design/admin/explayouts/rule_edit' )}</p>
            <table class="list" cellspacing="0">
                <tr><th>{'Type'|i18n( 'design/admin/explayouts/rule_edit' )}</th><th>{'Value'|i18n( 'design/admin/explayouts/rule_edit' )}</th></tr>
                {foreach $targets as $t}
                    <tr>
                        <td><input type="text" name="TargetType[]" value="{$t.target_type|wash}" /></td>
                        <td><input type="text" name="TargetValue[]" value="{$t.target_value|wash}" size="60" /></td>
                    </tr>
                {/foreach}
                {for 0 to 2 as $i}
                    <tr>
                        <td><input type="text" name="TargetType[]" value="" /></td>
                        <td><input type="text" name="TargetValue[]" value="" size="60" /></td>
                    </tr>
                {/for}
            </table>

            <h3>{'Conditions'|i18n( 'design/admin/explayouts/rule_edit' )}</h3>
            <p>{'All conditions must match. Types: <code>siteaccess</code>.'|i18n( 'design/admin/explayouts/rule_edit' )}</p>
            <table class="list" cellspacing="0">
                <tr><th>{'Type'|i18n( 'design/admin/explayouts/rule_edit' )}</th><th>{'Value'|i18n( 'design/admin/explayouts/rule_edit' )}</th></tr>
                {foreach $conditions as $c}
                    <tr>
                        <td><input type="text" name="ConditionType[]" value="{$c.condition_type|wash}" /></td>
                        <td><input type="text" name="ConditionValue[]" value="{$c.condition_value|wash}" size="60" /></td>
                    </tr>
                {/foreach}
                {for 0 to 2 as $i}
                    <tr>
                        <td><input type="text" name="ConditionType[]" value="" /></td>
                        <td><input type="text" name="ConditionValue[]" value="" size="60" /></td>
                    </tr>
                {/for}
            </table>

            <input class="defaultbutton" type="submit" name="SaveRule" value="{'Save rule'|i18n( 'design/admin/explayouts/rule_edit' )}" />
            <a class="button" href={'explayouts/rule_list'|ezurl}>{'Back'|i18n( 'design/admin/explayouts/rule_edit' )}</a>
        </form>
    </div>
</div>
