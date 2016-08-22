<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="recycle" rel="{$row.id}">
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>
		<td>{$row.recycle_time|date_format:'Y-m-d H:i:s'}</td>			
		<td>{$row.nickname}</td>			
		<td>{$types[$row.recycle_type]}</td>
		<td>{$row.meta}</td>
		<td class="text-right">			
			{if $canRestoreRecycle}
			<a title="还原" class="btn btn-xs btn-success" 
				href="{'system/recycle/restore'|app}{$row.id}" target="ajax"
				data-confirm="你确定要还原这个内容吗?">
				<i class="fa fa-fw fa-undo"></i></a>
			{/if}
			{if $canEmptyRecycle}
			<a title="删除" class="btn btn-xs btn-danger" 
				href="{'system/recycle/del'|app}{$row.id}" target="ajax"
				data-confirm="你确定要删除这个内容吗?">
				<i class="glyphicon glyphicon-trash"></i></a>
			{/if}
		</td>	
	</tr>
	{foreachelse}
	<tr>
		<td colspan="6">无内容</td>
	</tr>
	{/foreach}
</tbody>