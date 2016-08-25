<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="chunk" rel="{$row.id}">
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>
		<td>{$row.id}</td>
		<td>
			{if $canEditChunk}
			<a href="{'cms/chunk/edit'|app}{$row.id}" target="tag" data-tag="#content">
				{$row.name}</a>
			{else}
			{$row.name}
			{/if}
		</td>
		<td>{$row.catelogName}</td>
		<td class="hidden-xs hidden-sm">{$row.keywords}</td>	
		<td class="text-right">
			{if $canDelChunk}
			<a title="删除" class="btn btn-xs btn-danger" 
				href="{'cms/chunk/del'|app}{$row.id}" target="ajax"
				data-confirm="你确定要删除这个碎片吗?">
				<i class="glyphicon glyphicon-trash"></i></a>
			{/if}
		</td>	
	</tr>
	{foreachelse}
	<tr>
		<td colspan="6">无结果</td>
	</tr>
	{/foreach}
</tbody>