<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="ads" rel="{$row.id}">
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>
		<td>
			{if $canEditPV}
			<a href="#{'mobiapp/pageview/edit'|app:0}{$row.id}"><strong>{$row.name}</strong></a>
			{else}
			<strong>{$row.name}</strong>
			{/if}			
		</td>		
		<td>{$row.refid}</td>
		<td>{$row.tpl}</td>
		<td>{$row.models}</td>
		<td>{$row.update_time|date_format:'Y-m-d H:i'}</td>
		<td class="text-right">
			<div class="btn-group">
				{if $canEditPV}
				<a class="btn btn-primary btn-xs" href="#{'mobiapp/pageview/edit'|app:0}{$row.id}">
					<i class="fa fa-fw fa-edit"></i></a>
				{/if}				
				{if $canDelPV}
				<a class="btn btn-danger btn-xs"
					href="{'mobiapp/pageview/del'|app}{$row.id}" target="ajax"
					data-confirm="你确定要删除这个模板吗?">
					<i class="glyphicon glyphicon-trash"></i></a>
				{/if}
			</div>
		</td>
	</tr>
	{foreachelse}
	<tr>
		<td></td>
		<td colspan="7">无记录</td>
	</tr>
	{/foreach}
</tbody>