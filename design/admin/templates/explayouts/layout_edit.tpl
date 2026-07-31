<div class="context-block">
    <div class="box-header"><h1 class="context-title">Edit Layout: {$layout.name|wash}</h1></div>
    <div class="box-ml">
        {if $message}<div class="message-feedback">{$message|wash}</div>{/if}
        {if $error}<div class="message-error">{$error|wash}</div>{/if}

        <form method="post" action={concat('explayouts/layout_edit/',$layout.id)|ezurl}>
            <label>Identifier:</label>
            <input type="text" name="Identifier" value="{$layout.identifier|wash}" size="60" /><br/><br/>

            <label>Name:</label>
            <input type="text" name="Name" value="{$layout.name|wash}" size="60" /><br/><br/>

            <label>Layout type:</label>
            <select name="LayoutType">
                <option value="">-- Select --</option>
                {foreach $available_types as $typeInfo}
                    <option value="{$typeInfo.identifier|wash}" {if eq($layout.layout_type,$typeInfo.identifier)}selected="selected"{/if}>{$typeInfo.name|wash}</option>
                {/foreach}
            </select>
            <em>Selecting a type auto-creates zones on save.</em><br/><br/>

            <input class="button" type="submit" name="SaveDraft" value="Save draft" />
            <input class="defaultbutton" type="submit" name="Publish" value="Publish" />
            <input class="button" type="button" value="Cancel" onclick="window.location.href='/explayouts/layout_list';" />
        </form>

        <hr/>

        <h2>Zones</h2>
        {if count($zones)}
            {foreach $zones as $zoneEntry}
                {def $zone=$zoneEntry.zone}
                {def $blocks=$zoneEntry.blocks}
                <div style="border:1px solid #ccc;margin:1rem 0;padding:1rem;">
                    <h3>Zone: {$zone.identifier|wash}</h3>

                    <form method="post" action={concat('explayouts/layout_edit/',$layout.id)|ezurl} style="display:inline;margin:0;">
                        <input type="hidden" name="DeleteZoneID" value="{$zone.id|wash}" />
                        <button type="submit" name="DeleteZone" class="button" onclick="return confirm('Delete this zone and all its blocks?');">Delete zone</button>
                    </form>

                    {if count($blocks)}
                        <table class="list" cellspacing="0" style="margin-top:1rem;">
                            <tr><th>Position</th><th>Name</th><th>Type</th><th>&nbsp;</th></tr>
                            {foreach $blocks as $block}
                                <tr>
                                    <td>{$block.position|wash}</td>
                                    <td><strong>{$block.name|wash}</strong></td>
                                    <td>{$block.definition_identifier|wash}</td>
                                    <td>
                                        <a href={concat('explayouts/block_edit/',$block.id)|ezurl}>Edit</a>

                                        <form method="post" action={concat('explayouts/layout_edit/',$layout.id)|ezurl} style="display:inline;margin:0;">
                                            <input type="hidden" name="MoveBlockID" value="{$block.id|wash}" />
                                            <button type="submit" name="MoveBlockUp" class="button">Up</button>
                                            <button type="submit" name="MoveBlockDown" class="button">Down</button>
                                        </form>

                                        <form method="post" action={concat('explayouts/layout_edit/',$layout.id)|ezurl} style="display:inline;margin:0;">
                                            <input type="hidden" name="MoveBlockID" value="{$block.id|wash}" />
                                            <select name="TargetZoneID">
                                                {foreach $zones as $z}
                                                    {if ne($z.zone.id,$zone.id)}
                                                        <option value="{$z.zone.id|wash}">{$z.zone.identifier|wash}</option>
                                                    {/if}
                                                {/foreach}
                                            </select>
                                            <button type="submit" name="MoveBlockToZone" class="button">Move</button>
                                        </form>

                                        <form method="post" action={concat('explayouts/layout_edit/',$layout.id)|ezurl} style="display:inline;margin:0;">
                                            <input type="hidden" name="DeleteBlockID" value="{$block.id|wash}" />
                                            <button type="submit" name="DeleteBlock" class="button" onclick="return confirm('Delete this block?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            {/foreach}
                        </table>
                    {else}
                        <p>No blocks yet.</p>
                    {/if}

                    {if count($available_blocks)}
                        <form method="post" action={concat('explayouts/layout_edit/',$layout.id)|ezurl} style="margin-top:.5rem;">
                            <input type="hidden" name="ZoneID" value="{$zone.id|wash}" />
                            <select name="DefinitionIdentifier">
                                {foreach $available_blocks as $blockInfo}
                                    <option value="{$blockInfo.identifier|wash}">{$blockInfo.name|wash}</option>
                                {/foreach}
                            </select>
                            <input class="button" type="submit" name="AddBlock" value="Add block" />
                        </form>
                    {/if}
                </div>
            {/foreach}
        {else}
            <p>No zones yet. Choose a layout type and save to auto-create zones.</p>
        {/if}

        <form method="post" action={concat('explayouts/layout_edit/',$layout.id)|ezurl}>
            <input type="text" name="ZoneIdentifier" value="" size="30" placeholder="zone identifier" />
            <input class="button" type="submit" name="AddZone" value="Add custom zone" />
        </form>
    </div>
</div>
