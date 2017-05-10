<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="page" rel="{$row.id}">		
		<td>
			{if $row.id != 'cursouel'}
			<input type="checkbox" value="{$row.id}" class="grp" />
			{/if}
		</td>
		<td>{if $listTypes[$row.list_view]}{$listTypes[$row.list_view]['name']}{else}无{/if}</td>
		<td>
			<div class="row">
				<div class="col col-lg-3 col-md-2 hidden-xs hidden-sm">&nbsp;</div>
				<div class="col col-lg-6 col-md-8 col-xs-12 col-sm-12">
				<div class="mobi-box">
				{if $row.list_view}
					{if $listTypes[$row.list_view]}
					{$lv = $listTypes[$row.list_view]}					
					{$lv.clz|render:$row}
					{else}
					无法预览,未知样式
					{/if}
				{else}无法预览,请选择样式{/if}
				</div>
				</div>
				<div class="col col-lg-3 col-md-2 hidden-xs hidden-sm">&nbsp;</div>
			</div>
		</td>
		<td>{if $row.id !== 'cursouel'}{$row.page_view_name}{elseif $row.page_view_name}{$row.page_view_name}{else}{/if}</td>
		<td>
		{if $row.id !== 'cursouel'}<input type="text" value="{$row.sort}" class="mb-pg-sort" style="width:45px" maxlength="4" />{/if}
		</td>
		<td class="text-right">
			<div class="btn-group">
			{if $row.id == 'cursouel'}
			<a class="btn btn-primary btn-xs" href="#{'mobiapp/page/cursouel'|app:0}{$channel}">
					<i class="fa fa-fw fa-list"></i></a>
			{else}
			<a class="btn btn-primary btn-xs" onclick="return MobiApp.editpage({$row.id});">
					<i class="fa fa-fw fa-edit"></i></a>
			<a title="删除" class="btn btn-danger btn-xs"
				href="{'mobiapp/page/del'|app}{$row.id}" target="ajax"
				data-confirm="你确定要删除这个内容吗?">
				<i class="glyphicon glyphicon-trash"></i></a>
			{/if}
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