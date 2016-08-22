<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="media" rel="{$row.id}">
		<td><input type="checkbox" value="{$row.id}" class="grp"/></td>
		<td>{$row.name}</td>
		<td>{$row.appkey}</td>	
		<td>{$row.appsecret}</td>
		<td>{$row.callback_url}</td>
		<td class="text-center">
			{if $canEditApp}
			<a href="#{'rest/app/edit'|app:0}{$row.id}" class="btn btn-xs btn-primary">
				<i class="fa fa-pencil-square-o"></i></a>
			{/if}
			{if $canDelApp}
			<a title="删除" class="btn btn-xs btn-danger" 
				href="{'rest/app/del'|app}{$row.id}" target="ajax"
				data-confirm="你确定要删除这个应用吗?">
				<i class="glyphicon glyphicon-trash"></i></a>
			{/if}
		</td>	
	</tr>
	{foreachelse}
	<tr>
		<td colspan="6" class="text-center">无接入应用</td>
	</tr>
	{/foreach}
</tbody>