<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="page" rel="{$row.id}">
		<td class="hidden-xs hidden-sm"></td>
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>
		<td>{$row.id}</td>
		<td>	
			{if $row.channel=='_t'}		
				{$row.title2|truncate:24:'...'}[ URL规则:{$row.url|escape} ]
			{else}
			<a href="{$row|url}?preview" target="_blank">
				{$row.title2|truncate:24:'...'}</a>[ {$row.url|escape} ]
			{/if}			
			{if $row.flag_h}<span class="label bg-color-redLight">头条</span>{/if}
			{if $row.flag_c}<span class="label bg-color-orange">推荐</span>{/if}
			{if $row.flag_a}<span class="label bg-color-teal">特荐</span>{/if}
			{if $row.flag_b}<span class="label bg-color-blueLight">加粗</span>{/if}
			{if $row.flag_j}<span class="label bg-color-greenLight">跳转</span>{/if}
			
		</td>	
		<td>
			{$row.template_file}
		</td>
		<td>
			{$handlers[$row.url_handler]}
		</td>
		{if $enable_approving}
		<td class="hidden-xs hidden-sm">{$row.status|status:$status}</td>
		{/if}
		<td class="hidden-xs hidden-sm">
			{$row.update_time|date_format:'Y-m-d H:i'}			
		</td>
		<td class="text-right">
			<div class="btn-group">
			{if $canEditPage}
			<a href="#{'cms/cpage/edit'|app:0}{$row.id}" class="btn btn-xs btn-primary">
				<i class="fa fa-pencil-square-o"></i></a>
			{/if}
			{if $canDelPage}
			<a title="删除" class="btn btn-xs btn-danger" 
				href="{'cms/page/del'|app}{$row.id}" target="ajax"
				data-confirm="你确定要删除这个文章吗?">
				<i class="glyphicon glyphicon-trash"></i></a>
			{/if}			
			</div>
		</td>	

	</tr>
	<tr parent="{$row.id}" class="hidden-xs hidden-sm">
		<td colspan="2"></td>
		<td colspan="{if $disable_approving}6{else}5{/if}">
			<p>
				由{$row.cuname}创建于{$row.create_time|date_format:'Y-m-d H:i'}
				{if $cCache}
					[<a href="{$row.url|url}?preview=_c2c_" target="_blank">清除缓存</a>]
				{/if}
			</p>
			<p>关键词:{$row.keywords}</p>
			<p>内容模型:{$row.modelName}</p>
			<p>模板:{$row.template_file}</p>
		</td>
	</tr>
	{foreachelse}
	<tr class="hidden-xs hidden-sm">
		<td colspan="{if $disable_approving}8{else}7{/if}">无结果</td>
	</tr>
	{/foreach}
</tbody>