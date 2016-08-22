<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="tag" rel="{$row.id}">
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>		
		<td>
			{if $canEditTag}
			<a href="#{'cms/tag/edit'|app:0}{$row.id}">
				{$row.tag}</a>
			{else}
			{$row.tag}
			{/if}
		</td>
		<td class="hidden-xs hidden-sm">{$row.title}</td>
		<td class="hidden-xs hidden-sm">{$row.url}</td>
		<td class="text-right">			
			{if $canDelTag}
			<a title="删除" class="btn btn-xs btn-danger" 
				href="{'cms/tag/del'|app}{$row.id}" target="ajax"
				data-confirm="你确定要删除这个内链吗?">
				<i class="glyphicon glyphicon-trash"></i></a>
			{/if}
		</td>	
	</tr>
	{foreachelse}
	<tr>
		<td colspan="5">无结果,立即新增.</td>
	</tr>
	{/foreach}
</tbody>