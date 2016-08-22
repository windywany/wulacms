<tbody data-total="{$total}">
	{foreach $apps as $app}
	<tr>
		<td>
			<strong>{$app.appname}</strong>								
			{if $app.status=='0'}
			<span class="label label-default">禁用</span>
			{elseif $app.upgradable}
			<span class="label label-warning">可升级到{$app.currentVer}</span>
			{/if}
			{if $app.urlmapping}
			<p  style="margin:10px 0 0;"><a href="{'system/plugin/mapping'|app}{$app.name}" dialog-id="urlmapDialog" dialog-width="300" dialog-model="true" dialog-title="设置URL" target="dialog" title="">{$app.urlmapping}</a>
				{if $app.conflict}<span class="bg-color-red">URL冲突,建议修改.</span>{/if}
			</p>								
			{/if}
		</td>
		<td>{$app.installedVer}</td>							
		<td class="hidden-xs hidden-sm">
			<p>{$app.desc|escape|nl2br}</p>
			<p style="margin:5px 0 0;" class="text-info">
                最新版本:{$app.currentVer} | 
                作者:{$app.author} |
                <a target="_blank" href="{$app.website}">插件主页</a>
            </p>
		</td>
		<td class="text-right">
			{if $app.upgradable}
			<a class="btn btn-success btn-xs"
				href="{'system/plugin/upgrade'|app}{$app.name}/{$app.currentVer}"
				target="ajax"
				data-confirm="你确定要升级此应用到最新版本吗?"
				title="升级" 
				><i class="fa fa-cloud-upload"></i></a>
			{/if}
			{if $installed && $app.status && !$app.system}
			<a class="btn btn-warning btn-xs"
				href="{'system/plugin/disable'|app}{$app.name}"
				target="ajax"
				data-confirm="你确定要禁用此应用吗?"
				title="禁用" 
				><i class="fa fa-times-circle"></i></a>
			{elseif $installed && !$app.status && !$app.system}
			<a 	class="btn btn-success btn-xs"
				href="{'system/plugin/enable'|app}{$app.name}"
				target="ajax"
				data-confirm="你确定要启用此应用吗?"
				title="启用" 
				><i class="fa fa-check-circle"></i></a>
			{elseif !$installed}
			<a class="btn btn-success btn-xs"
				href="{'system/plugin/install'|app}{$app.name}"
				target="ajax"
				data-confirm="你确定要安装此应用吗?"
				><i class="fa fa-cloud-download"></i></a>
			{/if}
			{if $installed && !$app.system}
			<a class="btn btn-danger btn-xs"
				href="{'system/plugin/uninstall'|app}{$app.name}"
				target="ajax"
				data-confirm="插件卸载后其所有数据有可能全部被删除,你确定要卸载此应用吗?"
				><i class="fa fa-trash-o"></i></a>
			{/if}								
		</td>						
	</tr>
	{foreachelse}
	<tr><td colspan="4" class="">无可用插件</td></tr>
	{/foreach}	
</tbody>