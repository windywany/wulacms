<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="ads" rel="{$row.id}">
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>
		<td>
			{if $canEditVer}
			<a href="#{'mobiapp/ads/edit'|app:0}{$row.id}"><strong>{$row.name}</strong></a>
			{else}
			<strong>{$row.name}</strong>
			{/if}
		</td>		
		<td  class="hidden-xs hidden-sm">{$row.os}</td>
		<td  class="hidden-xs hidden-sm">{$row.banner}</td>
		<td  class="hidden-xs hidden-sm">{$row.bottom}</td>
		<td  class="hidden-xs hidden-sm">{$row.screen}</td>
		<td class="hidden-xs hidden-sm">{$row.stream}</td>
		<td class="hidden-xs hidden-sm">
			{$row.clickinsert}
		</td>
		<td class="hidden-xs hidden-sm">
			{$row.probability}
		</td>
		<td class="hidden-xs hidden-sm">{$row.update_time|date_format:'Y-m-d H:i'}</td>
		<td class="text-right">
			<div class="btn-group">
				<a class="btn btn-warning btn-xs"
					href="{'mobiapp/ads/copy'|app}{$row.id}" target="ajax"
					data-confirm="你确定要复制这个广告配置吗?" title="你确定要复制这个广告配置吗?">
					<i class="fa fa-fw fa-copy"></i></a>
				{if $canEditAds}
				<a class="btn btn-primary btn-xs" href="#{'mobiapp/ads/edit'|app:0}{$row.id}">
					<i class="fa fa-fw fa-edit"></i></a>			
				{/if}				
				{if $canDelAds}
				<a class="btn btn-danger btn-xs"
					href="{'mobiapp/ads/del'|app}{$row.id}" target="ajax"
					data-confirm="你确定要删除这个广告配置吗?">
					<i class="glyphicon glyphicon-trash"></i></a>
				{/if}
			</div>
		</td>
	</tr>
	{foreachelse}
	<tr>
		<td></td>
		<td colspan="10">无记录</td>
	</tr>
	{/foreach}
</tbody>