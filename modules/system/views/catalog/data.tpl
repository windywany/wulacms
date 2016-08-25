<tbody data-total="{$total}" data-disable-tree="{$disable_tree}">
{foreach $items as $item}
<tr data-parent="true" rel="{$item.id}" parent="{$item.upid}">	
	<td>
		<a href="#{'system/catalog/edit'|app:0}{$item.id}">
			{$item.name}
		</a>							
	</td>
	{if !$hiddenID}
	<td>{$item.id}</td>
	<td>{$item.alias}</td>
	{/if}
	{if $data_col_tpl}
	{include $data_col_tpl}
	{/if}
	<td class="hidden-xs hidden-sm">
	{$item.note|escape}
	</td>							
	<td class="text-right">
		<div class="btn-group">
		{if $canAddCatalog && !$is_enum}
		<a class="btn btn-success btn-xs"
			href="#{'system/catalog/add'|app:0}{$catalogType}/{$item.id}"
			title="添加子{$catalogTitle}"><i class="glyphicon glyphicon-plus"></i></a>
		{/if}
		{if $canDeleteCatalog}
		<a href="{'system/catalog/del'|app}{$item.id}" 
			class="btn btn-danger btn-xs"
			data-confirm="你真的要删除这个{$catalogTitle}吗？"
			target="ajax"><i class="fa fa-trash-o"></i></a>
		{/if}
		</div>
	</td>
</tr>
{foreachelse}
{if $addingtip}
<tr>
	<td colspan="5">		
		{if $canAddCatalog}
		立即
		<a href="#{'system/catalog/add'|app:0}{$catalogType}">添加一个{$catalogTitle}</a>.
		{/if}
	</td>
</tr>
{/if}
{/foreach}
</tbody>