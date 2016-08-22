<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="page" rel="{$row.id}">		
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>
		<td class="media album">
			{if $row.image}<a class="pull-left" href="#{'album/pic'|app:0}{$row.id}"> <img class="media-object" src="{$row.image|media}"> </a>{/if}	
			<div class="media-body">
				<p class="media-heading"><a href="{$row|url}?preview" target="_blank">{$row.title|truncate:32:'...'}</a></p>
				{if $row.flag_h}<span class="label bg-color-redLight">头条</span>&nbsp;{/if}
				{if $row.flag_c}<span class="label bg-color-orange">推荐</span>&nbsp;{/if}
				{if $row.flag_a}<span class="label bg-color-teal">特荐</span>&nbsp;{/if}
				{if $row.flag_b}<span class="label bg-color-blueLight">加粗</span>&nbsp;{/if}	
			</div>									
		</td>
		<td class="hidden-xs hidden-sm">{$row.channelName}</td>				
		<td class="hidden-xs hidden-sm">
			{$row.cuname}
		</td>
		<td class="hidden-xs hidden-sm">
			{$row.update_time|date_format:'Y-m-d H:i'}
		</td>
		<td class="hidden-xs hidden-sm"><a href="#{'album/pic'|app:0}{$row.id}">{$row.album_cnt}</a></td>
		<td class="text-right">
			<div class="btn-group">
				{if $canEditPage}
				<a href="#{'cms/page/edit'|app:0}page/{$row.id}" class="btn btn-primary btn-xs">
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
				{if $canEditPage}
				<li><a href="#{'album/upload'|app:0}{$row.id}">
					<i class="glyphicon glyphicon-cloud-upload txt-color-green"></i> 上传</a></li>
				{/if}
				{if $canEditPage && $enableCopy}					
				<li><a href="#{'cms/page/edit'|app:0}page/{$row.id}/1">
					<i class="fa fa-copy txt-color-green"></i> 复制</a></li>
				{/if}
				
				{if $canDelPage}
				<li><a title="删除"
					href="{'cms/page/del'|app}{$row.id}" target="ajax"
					data-confirm="你确定要删除这个文章吗?">
					<i class="glyphicon glyphicon-trash txt-color-red"></i> 删除</a></li>
				{/if}
				</ul>	
			</div>		
		</td>	
	</tr>
	{foreachelse}
	<tr class="hidden-xs hidden-sm">
		<td></td>
		<td colspan="7">无结果</td>
	</tr>
	{/foreach}
</tbody>