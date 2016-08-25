<tbody data-total="{$total}">
	{foreach $items as $item}
	<tr rel="{$item.id}">
		<td>
			<a href="#{'system/catalog/'|app:0}{$item.type}/">
				{$item.name}
			</a>
		</td>
		<td>{$item.type}</td>
		<td>{if $item.is_enum}<span class="badge txt-color-green">是</span>{/if}</td>
		<td class="hidden-xs hidden-sm">{$item.note|escape}</td>							
		<td class="text-right">
			<div class="btn-group">
			{if $canEdit}
				<a class="btn btn-primary btn-xs" href="#{'system/catatype/edit'|app:0}{$item.id}">
					<i class="fa fa-pencil-square-o"></i>
				</a>
			{/if}
			{if $canDel}
			<a href="{'system/catatype/del'|app}{$item.id}" 
				class="btn btn-danger btn-xs"
				data-confirm="你真的要删除这个数据吗？"
				target="ajax"><i class="fa fa-trash-o"></i></a>
			{/if}
			</div>
		</td>
	</tr>
	{foreachelse}
	<tr>
		<td colspan="5">
			{if $search}未找到{else}还未定义{/if}数据项.
			{if $canAdd}
			你可立即
			<a href="#{'system/catatype/add'|app:0}">定义一个数据项</a>	
			{/if}				
		</td>
	</tr>
	{/foreach}	
</tbody>