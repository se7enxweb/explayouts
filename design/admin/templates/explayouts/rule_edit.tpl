<div class="context-block">
    <div class="box-header"><h1 class="context-title">Edit Rule</h1></div>
    <div class="box-ml">
        {if $message}<div class="message-feedback">{$message|wash}</div>{/if}
        {if $error}<div class="message-error">{$error|wash}</div>{/if}

        <form method="post" action={concat('explayouts/rule_edit/',$rule.id)|ezurl}>
            <label>Layout:</label>
            <select name="LayoutID">
                {foreach $layouts as $layout}
                    <option value="{$layout.id|wash}" {if eq($rule.layout_id,$layout.id)}selected="selected"{/if}>{$layout.name|wash} ({$layout.identifier|wash})</option>
                {/foreach}
            </select><br/><br/>

            <label>Priority:</label>
            <input type="text" name="Priority" value="{$rule.priority|wash}" size="10" /><br/><br/>

            <label>
                <input type="checkbox" name="Enabled" value="1" {if $rule.enabled}checked="checked"{/if} /> Enabled
            </label><br/><br/>

            <h3>Targets</h3>
            <p>First matching target wins. Types: <code>path_prefix</code>, <code>path</code>, <code>path_regex</code> (pattern without delimiters), <code>content_node</code> (node ID or URL alias).</p>
            <table class="list" cellspacing="0">
                <tr><th>Type</th><th>Value</th></tr>
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

            <h3>Conditions</h3>
            <p>All conditions must match. Types: <code>siteaccess</code>.</p>
            <table class="list" cellspacing="0">
                <tr><th>Type</th><th>Value</th></tr>
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

            <input class="defaultbutton" type="submit" name="SaveRule" value="Save rule" />
            <a class="button" href={'explayouts/rule_list'|ezurl}>Back</a>
        </form>
    </div>
</div>
