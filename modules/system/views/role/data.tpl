<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="role" rel="{$row.role_id}">
		<td><input type="checkbox" value="{$row.role_id}" class="grp" /></td>
		<td>{$row.role_id}</td>
		<td>
			{if $canEditRole}
			<a href="#{'system/role/edit'|app:0}{$row.role_id}">{$row.role_name}</a>
			{else}
			{$row.role_name}
			{/if}
		</td>
		<td class="hidden-xs hidden-sm">{$row.role}</td>
		<td class="hidden-xs hidden-sm">{$row.note|escape}</td>	
		<td><input type="text" value="{$row.priority}" class="ch-item-sort form-control" style="width:50px" maxlength="3" /></td>
		<td class="text-right">
			{if $hasAcl}
			<a title="权限设置" class="btn btn-xs btn-primary" href="#{'system/role/acl'|app:0}{$row.role_id}"><i class="fa fa-sliders"></i></a>
			{/if}
		</td>	
	</tr>
	{foreachelse}
	<tr>
		<td></td>
		<td colspan="6">无角色</td>
	</tr>
	{/foreach}
</tbody>