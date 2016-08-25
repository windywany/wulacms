<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="block" rel="{$row.id}">
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>		
		<td>
			{if $canEditBlock}
			<a  href="#{'cms/blockitem/'|app:0}{$row.id}">			
				{$row.name}</a>
			{else}
			{$row.name}
			{/if}
			{if $row.mcnt}({$row.mcnt}){/if}
		</td>
		<td>{$row.refid}</td>
		<td>{$row.catelogName}</td>
		<td>{$row.note|escape}</td>	
		<td class="text-right">
			<div class="btn-group">
			{if $canAddBlock}
			<a href="#{'cms/block/edit'|app:0}{$row.id}" class="btn btn-xs btn-primary">			
				<i class="fa fa-pencil-square-o"></i></a>
			<a class="btn btn-success btn-xs"
					href="#{'cms/blockfield'|app:0}{$row.refid}" title="自定义字段"> <i class="fa fa-book"></i>
				</a> 
			{/if}
			{if $canDelBlock}
			<a title="删除" class="btn btn-xs btn-danger" 
				href="{'cms/block/del'|app}{$row.id}" target="ajax"
				data-confirm="你确定要删除这个碎片吗?">
				<i class="glyphicon glyphicon-trash"></i></a>
			{/if}
			</div>
		</td>	
	</tr>
	{foreachelse}
	<tr>
		<td colspan="6" class="text-center">无结果</td>
	</tr>
	{/foreach}
</tbody>