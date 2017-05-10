<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="ads" rel="{$row.id}">
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>
		<td>{if $canEditVer} <a
			href="#{'mobiapp/version/info'|app:0}{$row.id}"> <strong> {$row.name}
					{if $row.id==1} [默认] {/if} </strong>
		</a> {else} <strong> {$row.name} {if $row.id==1} [默认] {/if} </strong>
			{/if}
		</td>
		<td class="hidden-xs hidden-sm">{$row.version}</td>
		<td class="hidden-xs hidden-sm">{$row.vername}</td>
		<td>
			<a id="apk-url-{$row.id}" href="{$row.url}" class="apk-cc">{if $row.url}复制{/if}</a>
			{if $row.apk_file}
			[<a href="{'mobiapp/apk/resource'|app}{$row.id}" id="generate-apk-{$row.id}" target="ajax">生成</a>]
			[<a href="{'mobiapp/apk/del'|app}{$row.id}" id="del-apk-{$row.id}" target="ajax">删除</a>]
			{/if}
		</td>
		<td class="hidden-xs hidden-sm">{$row.os}</td>
		<td class="hidden-xs hidden-sm">{$row.update_type}</td>

		<td class="hidden-xs hidden-sm">{$row.update_time|date_format:'Y-m-d
			H:i'}</td>

		<td class="text-right">
			<div class="btn-group">
				{if $canEditVer} <a class="btn btn-warning btn-xs"
					href="{'mobiapp/version/copy'|app}{$row.id}" target="ajax"
					data-confirm="你确定要复制这个版本信息吗?" title="你确定要复制这个版本信息吗?"> <i
					class="fa fa-fw fa-copy"></i></a> <a class="btn btn-primary btn-xs"
					href="#{'mobiapp/version/edit'|app:0}{$row.id}"> <i
					class="fa fa-fw fa-edit"></i></a> {/if} {if $canDelVer} <a
					class="btn btn-danger btn-xs"
					href="{'mobiapp/version/del'|app}{$row.id}" target="ajax"
					data-confirm="你确定要删除这个版本吗?"> <i class="glyphicon glyphicon-trash"></i></a>
				{/if}
			</div>
		</td>
	</tr>
	{foreachelse}
	<tr>
		<td></td>
		<td colspan="9">无记录</td>
	</tr>
	{/foreach}
</tbody>