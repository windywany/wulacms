<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="page" rel="{$row.id}">
		<td class="hidden-xs hidden-sm"></td>
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>
		<td>{$row.id}</td>
		<td>
			<a href="{$row|url}?preview" target="_blank" title="{$row.title}">
				{$row.title|truncate:32:'...'}</a>&nbsp; {if $row.image}<span class="badge bg-color-red">图</span>&nbsp;{/if}
			{if $row.flag_h}<span class="label bg-color-redLight">头条</span>&nbsp;{/if}
			{if $row.flag_c}<span class="label bg-color-orange">推荐</span>&nbsp;{/if}
			{if $row.flag_a}<span class="label bg-color-teal">特荐</span>&nbsp;{/if}
			{if $row.flag_b}<span class="label bg-color-blueLight">加粗</span>&nbsp;{/if}
			{if $row.flag_j}<span class="label bg-color-greenLight">跳转</span>&nbsp;{/if}		
		</td>
		<td class="hidden-xs hidden-sm">{$row.channelName}</td>
		{if $disable_approving}
		<td class="hidden-xs hidden-sm">{$row.status|status:$status}</td>
		{/if}
		<td class="hidden-xs hidden-sm">
            {$row.cuname}/{$row.uuname|default:$row.cuname}
		</td>
		<td class="hidden-xs hidden-sm">
			{$row.update_time|date_format:'Y-m-d H:i'}
		</td>
		{if $canEditPage}
			<td class="hidden-xs hidden-sm"><input type="text" value="{$row.display_sort}" class="ch-item-sort form-control" style="width:50px" maxlength="9" /></td>	
		{/if}
		<td class="text-right">
			<div class="btn-group">
				{if $canEditPage}
				<a href="#{'cms/page/edit'|app:0}{$type}/{$row.id}" class="btn btn-primary btn-xs">
					<i class="fa fa-pencil-square-o"></i></a>									
				{else}
				<button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle">
					<i class="fa fa-list"></i>
				</button>
				{/if}
				<button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu pull-right">
				{if $canEditPage && $enableCopy}					
				<li><a href="#{'cms/page/edit'|app:0}{$type}/{$row.id}/1">
					<i class="fa fa-copy txt-color-green"></i> 复制</a></li>
				{/if}
				{if $canEditTag}
				<li><a href="{'cms/tag/topic2tag'|app}{$row.id}" target="ajax" data-confirm="你真的要将它加入内链库吗?">
				<i class="fa fa-link"></i> 内链</a></li>
				{/if}
				{if $canDelPage}
				<li><a title="删除"
					href="{'cms/page/del'|app}{$row.id}" target="ajax"
					data-confirm="你确定要删除这个文章吗?">
					<i class="glyphicon glyphicon-trash txt-color-red"></i> 删除</a></li>
				{/if}
				{if $cCache}
					<li><a href="{$row|url}?preview=_c2c_" target="_blank"><i class="fa  fa-refresh txt-color-orange"></i> 清除缓存</a></li>
				{/if}
				{'get_page_actions'|fire:$row}
				</ul>	
			</div>		
		</td>	
	</tr>
	<tr parent="{$row.id}" class="hidden-xs hidden-sm">
		<td colspan="2"></td>
		<td colspan="{if $disable_approving}7{else}6{/if}">
			<p>
				创建于&nbsp;<span class="badge bg-color-greenLights">{$row.create_time|date_format:'Y-m-d H:i'}</span>&nbsp;
				最后修改&nbsp;<span class="badge bg-color-greenLights">{$row.update_time|date_format:'Y-m-d H:i'}</span>&nbsp;
				{if $row.publish_time}发布于&nbsp;<span class="badge bg-color-greenLights">{$row.publish_time|date_format:'Y-m-d H:i'}</span>.{/if}				
			</p>
			<p>内容模型:{$row.modelName}{if $row.keywords},&nbsp;关键词:{$row.keywords}{/if}</p>
			{'show_page_detail'|fire:$row}
		</td>
		{if $canEditPage}
		<td></td>
		{/if}
	</tr>
	{foreachelse}
	<tr class="hidden-xs hidden-sm">
		<td></td>
		<td colspan="{if $disable_approving}8{else}7{/if}">无结果</td>
		{if $canEditPage}
		<td></td>
		{/if}
	</tr>
	{/foreach}
</tbody>