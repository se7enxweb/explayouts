<div class="block">
<table class="list" cellspacing="0" summary="Authors and publication information">
<tr>
    <th>Published</th>
    <th>Modified</th>
    <th>Creator</th>
    <th>Last contributor</th>
</tr>
<tr class="bglight">
    <td>{$node.object.published|l10n(shortdatetime)}</td>
    <td>{$node.object.modified|l10n(shortdatetime)}</td>
    <td><a href={$node.object.owner.main_node.url_alias|ezurl}>{$node.object.owner.name|wash}</a></td>
    <td><a href={$node.object.current.creator.main_node.url_alias|ezurl}>{$node.object.current.creator.name|wash}</a></td>
</tr>
</table>
</div>