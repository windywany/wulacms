<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="page" rel="{$row.id}">		
		<td>			
			<input type="checkbox" value="{$row.id}" class="grp" />			
		</td>
		<td>
			<img src="{$row.custom_data.image|media}" width="100%"/>
		</td>
		<td>{if $row.custom_data.title}{$row.custom_data.title}{else}{$row.title}{/if}</td>		
		<td>{if $row.page_view_name}
				{$row.page_view_name}
			{/if}
		</td>
		<td><input type="text" value="{$row.sort}" class="mb-pg-sort" style="width:45px" maxlength="4" /></td>
		<td class="text-right">
			<div class="btn-group">
			<a class="btn btn-primary btn-xs" onclick="return MobiApp.editpage({$row.id});">
					<i class="fa fa-fw fa-edit"></i></a>			
			<a title="删除" class="btn btn-danger btn-xs"
				href="{'mobiapp/page/del'|app}{$row.id}" target="ajax"
				data-confirm="你确定要删除这个内容吗?">
				<i class="glyphicon glyphicon-trash"></i></a>			
			</div>
		</td>
	</tr>
	{foreachelse}
	<tr class="hidden-xs hidden-sm">
		<td></td>
		<td colspan="5">无结果</td>		
	</tr>
	{/foreach}
</tbody>