{* The template block "image": the image of the page the layout is drawn for (a "Content field" block of the Netgen
   site), in any design. The page's node: $node where a full view defines it, else the node of
   $module_result.content_info (the page layout and the layout preview set it). The field: the first of
   explayouts.ini [PageImageSettings] AttributeIdentifiers[] that has content, drawn in ImageAlias. A design that draws
   the image its own way overrides explayouts/tpl_block/image.tpl (the starter design of cjw_themes_jumper does).
   Width and height come from the alias, so the page does not jump while the image loads; the look is
   .slb-page-image in explayouts.css (no inline style). *}
{def $pi_node = false()
     $pi_attr = false()}
{if and( is_set( $node ), is_object( $node ) )}
    {set $pi_node = $node}
{elseif and( is_set( $module_result.content_info.node_id ), $module_result.content_info.node_id|gt( 0 ) )}
    {set $pi_node = fetch( 'content', 'node', hash( 'node_id', $module_result.content_info.node_id ) )}
{/if}
{if $pi_node}
    {foreach ezini( 'PageImageSettings', 'AttributeIdentifiers', 'explayouts.ini' ) as $pi_id}
        {if and( $pi_attr|not, is_set( $pi_node.data_map[$pi_id] ), $pi_node.data_map[$pi_id].data_type_string|eq( 'ezimage' ), $pi_node.data_map[$pi_id].has_content )}
            {set $pi_attr = $pi_node.data_map[$pi_id]}
        {/if}
    {/foreach}
{/if}
{if $pi_attr}
{def $pi_alias = $pi_attr.content[ezini( 'PageImageSettings', 'ImageAlias', 'explayouts.ini' )]}
<figure class="slb-page-image"><img src={$pi_alias.url|ezroot} width="{$pi_alias.width|wash}" height="{$pi_alias.height|wash}" alt="{cond( $pi_attr.content.alternative_text|ne( '' ), $pi_attr.content.alternative_text, $pi_node.name )|wash}" /></figure>
{undef $pi_alias}
{/if}
{undef $pi_node $pi_attr}
