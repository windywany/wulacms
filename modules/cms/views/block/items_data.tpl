<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="blockitem" rel="{$row.id}">
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>			
		<td>			
			<a href="{$row.url}" target="_blank">{$row.title}</a>			
			{if $row.page_id}[{$row.page_id}]{/if}
		</td>		
		<td>{if $row.image}有{/if}</td>
		<td>{$row.description|escape}</td>
		<td><input type="text" value="{$row.sort}" class="ch-item-sort form-control" style="width:50px" maxlength="3" /></td>
		<td class="text-center">	
			<div class="btn-group">	
			{if $canEditBlock}
			<a class="btn btn-xs btn-primary"  href="#{'cms/blockitem/edit'|app:0}{$row.id}">
				<i class="fa fa-pencil-square-o"></i></a>
			{/if}	
			{if $canDelBlock}
			<a title="删除" class="btn btn-xs btn-danger" 
				href="{'cms/blockitem/del'|app}{$row.id}" target="ajax"
				data-confirm="你确定要删除这个内容吗?">
				<i class="glyphicon glyphicon-trash"></i></a>
			{/if}
			</div>
		</td>	
	</tr>
	{foreachelse}
	<tr>
		<td colspan="7" class="text-center">无结果,
			<a href="#{'cms/blockitem/add'|app:0}{$block}">立即新增</a>.
		</td>
	</tr>
	{/foreach}
</tbody>