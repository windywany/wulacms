<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="ads" rel="{$row.id}">
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>
		<td>{$row.market_name}</td>
		<td>
			{if $canEditVer}
			<a href="#{'mobiapp/version/editinfo'|app:0}{$row.id}"><strong>{$row.market}</strong></a>
			{else}
			<strong>{$row.market}</strong>
			{/if}
		</td>		
		<td>
			<a id="apk-url-{$row.id}" href="{$row.url}" class="apk-cc">{if $row.url}复制{/if}</a>			
			[<a href="{'mobiapp/apk/generate'|app}{$row.id}" id="generate-apk-{$row.id}" target="ajax">生成</a>]
			[<a href="{'mobiapp/apk/delapk'|app}{$row.id}" id="del-apk-{$row.id}" target="ajax">删除</a>]
		</td>
		<td>{$row.ad_name}</td>
		<td>{$row.update_time|date_format:'Y-m-d H:i'}</td>
		<td class="text-right">
			<div class="btn-group">
				{if $canEditVer}
				<a class="btn btn-primary btn-xs" href="#{'mobiapp/version/editinfo'|app:0}{$row.id}">
					<i class="fa fa-fw fa-edit"></i></a>			
				{/if}				
				{if $canDelVer}
				<a class="btn btn-danger btn-xs"
					href="{'mobiapp/version/delinfo'|app}{$row.id}" target="ajax"
					data-confirm="你确定要删除这个市场吗?">
					<i class="glyphicon glyphicon-trash"></i></a>
				{/if}
			</div>
		</td>
	</tr>
	{foreachelse}
	<tr>
		<td></td>
		<td colspan="5">无记录</td>
	</tr>
	{/foreach}
</tbody>