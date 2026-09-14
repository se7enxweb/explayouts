{* Contributors: everyone who created a version of this object, de-duplicated.

   The object carries one version per edit, each recording its creator, so the
   distinct creators across every version are the people who have worked on it.
   $node.object.owner is only the original author and
   $node.object.current.creator only the most recent one, so neither answers
   "who has touched this".

   Built with contains() rather than unique() because the same person appears
   once per version they saved, and an object edited for years can hold far more
   versions than contributors. *}
{def $tab_contributors = array()}
{foreach $node.object.versions as $tab_version}
    {if $tab_version.creator}
        {if not( $tab_contributors|contains( $tab_version.creator.name ) )}
            {set $tab_contributors = $tab_contributors|append( $tab_version.creator.name )}
        {/if}
    {/if}
{/foreach}
{set $tab_contributors = $tab_contributors|sort()}

<div class="block">
<table class="list" cellspacing="0" summary="Authors and publication information">
<tr>
    <th>Published</th>
    <th>Modified</th>
    <th>Creator</th>
    <th>Last contributor</th>
    <th>Contributors{if $tab_contributors|count|gt(1)} ({$tab_contributors|count}){/if}</th>
</tr>
<tr class="bglight">
    <td style="white-space: nowrap;">{$node.object.published|l10n(shortdatetime)}</td>
    <td style="white-space: nowrap;">{$node.object.modified|l10n(shortdatetime)}</td>
    <td style="white-space: nowrap;"><a href={$node.object.owner.main_node.url_alias|ezurl}>{$node.object.owner.name|wash}</a></td>
    <td style="white-space: nowrap;"><a href={$node.object.current.creator.main_node.url_alias|ezurl}>{$node.object.current.creator.name|wash}</a></td>
    {* max-width:0 with width:100% is what makes a table cell wrap instead of
       stretching the table: it lets this column take the leftover width and
       forces the text to break, however many names there are. The other cells
       are nowrap so a long list cannot squash the dates onto two lines. *}
    <td class="contributors" style="width: 100%; max-width: 0; white-space: normal; word-wrap: break-word; overflow-wrap: break-word; line-height: 1.7;">
        {if $tab_contributors|count|gt(0)}{$tab_contributors|implode(', ')|wash}{else}-{/if}
    </td>
</tr>
</table>
</div>
{undef $tab_contributors}
