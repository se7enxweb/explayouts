<div class="context-block">
    <div class="box-header"><h1 class="context-title">{'Exponential Layouts Setup'|i18n( 'design/admin/explayouts/setup' )}</h1></div>
    <div class="box-ml">
        {if $message}<div class="message-feedback">{$message|wash}</div>{/if}
        {if $error}<div class="message-error">{$error|wash}</div>{/if}

        <p>{'Detected database type:'|i18n( 'design/admin/explayouts/setup' )} <code>{$db_type|wash}</code></p>

        {if $schema_file}
            <p>{'Schema file:'|i18n( 'design/admin/explayouts/setup' )} <code>{$schema_file|wash}</code></p>
            <form method="post" action={'explayouts/setup'|ezurl}>
                <input class="defaultbutton" type="submit" name="InstallSchema" value="{'Install / update schema'|i18n( 'design/admin/explayouts/setup' )}" onclick="return confirm('{'Run the schema DDL? Existing data in these tables will not be removed because CREATE TABLE IF NOT EXISTS is used.'|i18n( 'design/admin/explayouts/setup' )|wash( javascript )}');" />
            </form>
        {else}
            <p>{'Could not determine a supported schema file for this database.'|i18n( 'design/admin/explayouts/setup' )}</p>
        {/if}

        <form method="post" action={'explayouts/setup'|ezurl}>
            <input class="button" type="submit" name="InstallBaseData" value="{'Install base data'|i18n( 'design/admin/explayouts/setup' )}" onclick="return confirm('{'Install base data? This adds a default homepage layout and catch-all rule.'|i18n( 'design/admin/explayouts/setup' )|wash( javascript )}');" />
        </form>

        <form method="post" action={'explayouts/setup'|ezurl}>
            <input class="button" type="submit" name="InstallTestData" value="{'Install test data'|i18n( 'design/admin/explayouts/setup' )}" onclick="return confirm('{'Install test data? This adds demo layouts and resolver rules.'|i18n( 'design/admin/explayouts/setup' )|wash( javascript )}');" />
        </form>

        <p><a href={'explayouts/layout_list'|ezurl} class="button">{'Back to layouts'|i18n( 'design/admin/explayouts/setup' )}</a></p>
    </div>
</div>
