<tbody data-total="0" data-disable-tree="{$search}">
{foreach $items as $item}
<tr rel="{$item.id}" parent="{$item.upid}" data-parent="true">		
	<td class="forumname"><span>{$item.name}</span></td>
</tr>
{/foreach}
</tbody>