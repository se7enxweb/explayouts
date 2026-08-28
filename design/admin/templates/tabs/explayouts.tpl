{ezcss_load(array('netgen/layouts-admin.css','netgen/layouts-ibexa.css','nglayouts-ui.css'))}
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
{def $resolved = fetch('explayouts','resolve_layout_for_node',hash('node_id',$node.node_id))}
{def $rules = fetch('explayouts','rules_for_node',hash('node_id',$node.node_id))}
{literal}<style>
.exp-node-layout-tab { padding: 0px; }
.exp-node-layout-tab .layouts-header { margin-bottom: 8px; }
.nl-node-resolved { background: #fff; border: 1px solid #e0e0e0; border-radius: 4px; padding: 12px; margin-bottom: 10px; }
.nl-node-resolved-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 6px; }
.nl-node-resolved-title { font-size: 16px; font-weight: 500; color: #333; margin: 0 0 2px; }
.nl-node-resolved-meta { font-size: 11px; color: #777; margin: 0; }
.nl-node-resolved-stats { display: flex; gap: 12px; margin: 8px 0; }
.nl-node-resolved-stat { display: flex; align-items: center; gap: 4px; font-size: 12px; color: #555; }
.nl-node-resolved-stat i { color: #666; font-size: 16px; }
.nl-node-rules-title { font-size: 14px; font-weight: 500; color: #333; margin: 0 0 6px; }
.nl-node-rule { background: #fff; border: 1px solid #e0e0e0; border-radius: 4px; padding: 8px 12px; margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between; }
.nl-node-rule-info { min-width: 0; }
.nl-node-rule-name { font-size: 13px; font-weight: 500; color: #333; margin: 0 0 2px; }
.nl-node-rule-meta { font-size: 11px; color: #777; margin: 0; }
.nl-node-rule-actions { display: flex; align-items: center; gap: 6px; }
.nl-status { display: inline-flex; align-items: center; padding: 2px 6px; border-radius: 12px; font-size: 10px; font-weight: 500; text-transform: uppercase; }
.nl-status.enabled { background: #e6f5e6; color: #2e7d32; }
.nl-status.disabled { background: #f5f5f5; color: #777; }
.nl-empty-state { background: #f9f9f9; border: 1px dashed #d0d0d0; border-radius: 4px; padding: 16px; text-align: center; color: #777; }
.nl-empty-state i { font-size: 32px; color: #bbb; margin-bottom: 6px; }
.nl-empty-text { color: #777; font-size: 13px; margin: 0 0 14px; }
.layouts-controls { margin-top: 12px; display: flex; justify-content: flex-end; gap: 8px; }
</style>{/literal}
<div class="ng-layouts-app row exp-node-layout-tab">
    <div class="layouts-content">
        <div class="layouts-header">
            <div>
                <p style="margin:0;color:#777;font-size:12px;">Node: {$node.name|wash} (ID: {$node.node_id}) · URL alias: {$node.url_alias|wash}</p>
            </div>
        </div>

        {if $resolved}
            <div class="nl-node-resolved">
                <div class="nl-node-resolved-header">
                    <div>
                        <h3 class="nl-node-resolved-title">Resolved layout</h3>
                        <p class="nl-node-resolved-meta">{$resolved.name|wash} ({$resolved.identifier|wash}) · Type: {$resolved.layout_type|wash}</p>
                    </div>
                    <div class="nl-node-rule-actions">
                        <a href={concat('explayouts_ui/layout_preview/',$resolved.id,'/2')|ezurl} target="_blank" class="nl-btn"><i class="material-icons">visibility</i> Preview</a>
                        <a href={concat('explayouts_ui_api/app#layout/',$resolved.id)|ezurl} class="nl-btn nl-btn-primary" onclick="sessionStorage.setItem('nglayouts_return_to','/content/view/full/{$node.node_id}'); return true;"><i class="material-icons">edit</i> Edit layout</a>
                    </div>
                </div>
                {def $zone_count = count($resolved.zones)}
                {def $block_count = $resolved.block_count}
                <div class="nl-node-resolved-stats">
                    <div class="nl-node-resolved-stat"><i class="material-icons">view_quilt</i> {$zone_count} zone{if $zone_count|ne(1)}s{/if}</div>
                    <div class="nl-node-resolved-stat"><i class="material-icons">widgets</i> {$block_count} block{if $block_count|ne(1)}s{/if}</div>
                    <div class="nl-node-resolved-stat"><i class="material-icons">assignment</i> Layout ID: {$resolved.id}</div>
                </div>
                {undef $zone_count $block_count}
            </div>
        {else}
            <div class="nl-empty-state" style="margin-bottom:22px;">
                <i class="material-icons">layers</i>
                <p>No layout resolves for this node.</p>
            </div>
        {/if}

        <h3 class="nl-node-rules-title">List of layout mappings directly applied to this location</h3>
        {if count($rules)}
            {foreach $rules as $rule}
                <div class="nl-node-rule">
                    <div class="nl-node-rule-info">
                        <p class="nl-node-rule-name"><a href={concat('explayouts_ui_api/app#layout/',$rule.layout_id)|ezurl}>{$rule.layout_name|wash}</a> <small>({$rule.layout_identifier|wash})</small></p>
                        <p class="nl-node-rule-meta">Priority {$rule.priority|wash} · Target: node / {$node.node_id}</p>
                    </div>
                    <div class="nl-node-rule-actions">
                        {if $rule.enabled}<span class="nl-status enabled">Enabled</span>{else}<span class="nl-status disabled">Disabled</span>{/if}
                        <a href={concat('explayouts_ui/rule_list?RuleID=',$rule.id)|ezurl} class="nl-btn" title="Edit mapping"><i class="material-icons">edit</i></a>
                        <a href={concat('explayouts_ui/layout_preview/',$rule.layout_id,'/2')|ezurl} target="_blank" class="nl-btn" title="Preview layout"><i class="material-icons">visibility</i></a>
                    </div>
                </div>
            {/foreach}
        {else}
            <p class="nl-empty-text">No mappings</p>
        {/if}

        <h3 class="nl-node-rules-title">List of layouts using this location/content as block item</h3>
        <p class="nl-empty-text">No related layouts</p>

        <h3 class="nl-node-rules-title">List of layouts using this content as a component</h3>
        <p class="nl-empty-text">No layouts</p>
        <div class="layouts-controls">
            <a href={concat('explayouts_ui/rule_list?TargetType=node&TargetValue=',$node.node_id)|ezurl} class="nl-btn nl-btn-primary">
                <i class="material-icons">add</i> Map layout
            </a>
            <a href={concat('explayouts_ui_api/app#layout')|ezurl} class="nl-btn">
                <i class="material-icons">add_box</i> New layout
            </a>
        </div>
    </div>
</div>
