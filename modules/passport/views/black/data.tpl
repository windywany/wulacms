<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="member" rel="{$row.id}">
		<td></td>
		<td><input type="checkbox" value="{$row.id}" class="grp"/></td>
		<td>{$row.id}</td>
		<td>
			<strong>			
				{$row.nickname}
			</strong>
		</td>		
		<td>{$row.create_time|date_format:'Y-m-d H:i'}</td>
		<td class="text-right">		
				{if $canEditMember}
					<a href="#{'passport/black/add'|app:0}{$row.id}" class="btn btn-primary btn-xs"><i class="fa fa-pencil-square-o"></i></a>
				{/if}
				{if $canDelMember}
				<a href="{'passport/black/del'|app}{$row.id}" data-confirm="你真的要删除吗？ "  class="btn btn-danger btn-xs" target="ajax"><i class="glyphicon glyphicon-trash"></i> </a>
				{/if}
		</td>		
	</tr>
	{foreachelse}
	<tr>
		<td colspan="6" class="text-center">无记录</td>
	</tr>
	{/foreach}
</tbody>