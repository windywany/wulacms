<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="user" rel="{$row.user_id}">
		<td></td>
		<td><input type="checkbox" value="{$row.user_id}" class="grp"/></td>
		<td>{$row.user_id}</td>
		<td>
			{if $canEditUser}
			<a href="#{'system/user/edit'|app:0}{$row.user_id}">{$row.username}</a>
			{else}
			{$row.username}
			{/if}
		</td>
		<td>{$row.nickname}</td>
		<td>{$row.group_name}</td>
		<td>{$row.roles}</td>
		<td>{$row.email}</td>
		<td class="text-center">{if $row.status}
			<span class="label label-success">正常</span>
			{else}
			<span class="label label-danger">禁用</span>
			{/if}
		</td>		
	</tr>
	<tr parent="{$row.user_id}">
		<td colspan="2"></td>
		<td colspan="7">暂无信息</td>
	</tr>
	{/foreach}
</tbody>