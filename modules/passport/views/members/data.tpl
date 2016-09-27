<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="member" rel="{$row.mid}">
		<td></td>
		<td><input type="checkbox" value="{$row.mid}" class="grp"/></td>
		<td>{$row.mid}</td>
		<td>
			<strong>			
			{if $canEditMember}
			<a href="#{'passport/members/edit'|app:0}{$row.mid}">{$row.username}</a>
			{else}
			{$row.username}
			{/if}({$row.nickname})</strong>{if $row.binder}<br/>[{$row.binder}]{/if}
		</td>		
		<td>{$row.group_name}</td>
		<td>{$row.roles}</td>
		<td>{$types[$row.type]}</td>
		<td>
			{if $row.email}<a href="mailto:{$row.email}">{$row.email}</a><br/>{/if}
			{if $row.phone}{$row.phone}{/if}
		</td>
		<td>
			{$row.registered|date_format:'Y-m-d'}
			{if $row.nickname1}
				<br/>
				推荐人:{$row.nickname1}
			{/if}
		</td>
		{if $enable_auth}
		<td>
			{if $auth_api_url}
				<a href="{$auth_api_url}?mid={$row.mid}&status={$row.auth_status}">{$auth_status[$row.auth_status]}</a>
			{else}
				{$auth_status[$row.auth_status]}
			{/if}
		</td>
		{/if}
		<td>
			{if $row.status == '1'}
			<span class="label label-success">正常</span>
			{elseif $row.status == '2'}
			<span class="label label-warning">未激活</span>
			{elseif $row.status == '3'}
			<span class="label label-primary">待激活</span>
			{else}
			<span class="label label-danger">禁用</span>
			{/if}
		</td>
		<td class="text-right">		
			<div class="btn-group">
				{if $canEditMember}
				<a href="#{'passport/members/edit'|app:0}{$row.mid}" class="btn btn-primary btn-xs"><i class="fa fa-pencil-square-o"></i></a>
				{else}
				<button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle">
					<i class="fa fa-list"></i>
				</button>
				{/if}
				<button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu pull-right">
					{'passport_member_actions'|fire:$row}
					{if $canDelMember}
					<li><a href="{'passport/members/del'|app}{$row.mid}" data-confirm="你真的要删除吗？"
						target="ajax"><i class="fa fa-trash-o text-danger"></i> 删除</a></li>
					{/if}
				</ul>
			</div>
		</td>		
	</tr>
	<tr parent="{$row.mid}">
		<td colspan="2"></td>
		<td colspan="{if $enable_auth}10{else}9{/if}">						
			{'render_member_extra'|fire:$row}
		</td>
	</tr>
	{foreachelse}
	<tr>
		<td colspan="{if $enable_auth}12{else}11{/if}" class="text-center">无记录</td>
	</tr>
	{/foreach}
</tbody>