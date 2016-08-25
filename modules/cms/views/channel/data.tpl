<tbody data-total="{$total}" data-disable-tree="{$search}">
	{foreach $items as $item}
	<tr rel="{$item.id}" parent="{$item.upid}" data-parent="{if $item.child_cnt>0}true{/if}">	
		<td>
			<a href="{$item|url}?preview" target="_blank">
			{$item.name}								
			</a>		
			{if $item.hidden}[隐]{/if}				
		</td>
		<td>{if !$item.isfinal}<span class="label label-success">是</span>{/if}</td>
		<td class="hidden-xs hidden-sm">{$item.modelName}</td>
		<td class="hidden-xs hidden-sm">{$item.refid}</td>
		<td class="hidden-xs hidden-sm"><input type="text" value="{$item.sort}" class="ch-item-sort form-control" style="width:50px" maxlength="3" /></td>						
		<td class="text-right">
			<div class="btn-group">
				{if $canEditChannel}
					<a href="#{'cms/channel/edit'|app:0}{$item.id}" class="btn btn-primary btn-xs">
					<i class="fa fa-pencil-square-o"></i></a>									
				{else}
					<a href="#{'cms/page'|app:0}{if $type}all/topic/{/if}?channel={$item.refid}" class="btn btn-primary btn-xs">
						<i class="fa fa-files-o"></i></a>
				{/if}									
				<button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu pull-right">
				{if $canEditChannel}
				<li><a href="#{'cms/page'|app:0}{if $type}all/topic/{/if}?channel={$item.id}">
				   <i class="fa fa-files-o"></i> 查看页面</a></li>
				{/if}
				{if $canAddChannel && !$type}
				<li><a href="#{'cms/channel/add'|app:0}{if $type}1{else}0{/if}/{$item.id}"
					title="添加子分类"><i class="glyphicon glyphicon-plus txt-color-green"></i> 添加子栏目</a></li>
				{/if}
				{if $canUpdateURL}
				<li>
					<a href="#" rel="{$item.id}" title="更新页面URL" class="update_page_url txt-color-orange">
						<i class="glyphicon glyphicon-link"></i> 更新页面URL</a>
				</li>
				{/if}
				{if $canDeleteChannel}
				<li><a href="{'cms/channel/del'|app}{$item.id}" 									
					data-confirm="你真的要删除这个{$channelType}吗？"
					target="ajax" class="txt-color-red"><i class="fa fa-trash-o"></i> 删除</a></li>
				{/if}
				</ul>
			</div>
		</td>
	</tr>
	{foreachelse}

	<tr>
		<td colspan="6">
			{if $search}无结果{else}
			无栏目.立即
			{if $canAddChannel}
			<a href="#{'cms/channel/add'|app:0}{if $type}1{else}0/{$_tid}{/if}">
				新增
			</a>
			{/if}
			{/if}
		</td>
	</tr>
	{/foreach}	
</tbody>