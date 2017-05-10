<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="ads" rel="{$row.id}">
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>
		<td>
			{if $canEditCH}
			<a href="#{'mobiapp/channel/edit'|app:0}{$row.id}"><strong>{$row.name}</strong></a>
			{else}
			<strong>{$row.name}</strong>
			{/if}{if !$row.hidden}[显]{/if}
		</td>		
		<td>{$row.refid}</td>
		<td>
			{foreach $row.binds as $b}
				{$b.name}
				{if !$b@last},{/if}
			{/foreach}
		</td>
		<td>{$row.update_time|date_format:'Y-m-d H:i'}</td>
		<td class="hidden-xs hidden-sm"><input type="text" value="{$row.sort}" class="ch-item-sort form-control" style="width:50px" maxlength="3" /></td>		
		<td class="text-right">
			<div class="btn-group">
				{if $canEditCH}
				<a class="btn btn-primary btn-xs" href="#{'mobiapp/channel/edit'|app:0}{$row.id}">
					<i class="fa fa-fw fa-edit"></i></a>
				{/if}				
				{if $canDelCH}
				<a class="btn btn-danger btn-xs"
					href="{'mobiapp/channel/del'|app}{$row.id}" target="ajax"
					data-confirm="你确定要删除这个栏目吗?">
					<i class="glyphicon glyphicon-trash"></i></a>
				{/if}
			</div>
		</td>
	</tr>
	{foreachelse}
	<tr>
		<td></td>
		<td colspan="6">无记录</td>
	</tr>
	{/foreach}
</tbody>