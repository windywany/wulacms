<tbody data-total="{$total}" data-disable-tree="{$search}">
	{foreach $models as $model}
	<tr data-parent="{if $model.child_cnt>0}true{/if}" rel="{$model.id}" parent="{$model.upid}">
		<td><a href="#{'cms/model/edit'|app:0}{$model.id}"> {$model.name} </a></td>
		<td class="hidden-xs hidden-sm">{$model.refid}</td>
		<td class="text-center hidden-xs hidden-sm">{if $model.is_topic_model}
			<span class="label label-success">是</span> {/if}
		</td>
		<td class="text-center hidden-xs hidden-sm">{if $model.status} <span
			class="label label-success">正常</span> {else} <span
			class="label label-danger">禁用</span> {/if}
		</td>
		<td>{if $model.role}{$groups[$model.role]}{else}文章{/if}</td>
		<td class="text-right">
			<div class="btn-group">
				<a class="btn btn-success btn-xs"
					href="#{'cms/modelfield'|app:0}{$model.refid}" title="自定义字段"> <i class="fa fa-book"></i>
				</a> 
				{if $canDeleteModel} 
					<a href="{'cms/model/del'|app}{$model.id}"
					class="btn btn-danger btn-xs" data-confirm="你真的要删除这个内容模型吗？"
					target="ajax"><i class="fa fa-trash-o"></i></a> 
				{/if}
			</div>
		</td>
	</tr>
	{/foreach}
</tbody>