<tbody data-total="{$total}" data-disable-tree="{$search}">
	{foreach $items as $item}
	<tr rel="{$item.id}" parent="{$item.upid}" data-parent="{if $item.child_cnt>0}true{/if}">	
		<td>
			<img src="{$item.headimgurl}" style="width:40px;">								
		</td>
		<td>
			{$item.nickname}								
		</td>
		<td>
			{$item.sexName}								
		</td>
		<td>
			{$item.city}								
		</td>
		<td>
			{$item.province}								
		</td>
		<td class="hidden-xs hidden-sm">
			{$item.subscribeName}
		</td>
		<td class="hidden-xs hidden-sm">
			{$item.subscribeTime}
		</td>
		<td class="hidden-xs hidden-sm">
			{$item.updateTime}
		</td>
		<td class="text-right">
			<div class="btn-group">
				{if $canDeleteFans}
				<a href="{'weixin/fans/del'|app}{$item.id}" 									
					data-confirm="你真的要删除这个用户吗？"
					target="ajax" class="txt-color-red"><i class="fa fa-trash-o"></i> 禁用</a>
				{/if}
			</div>
		</td>
	</tr>
	{foreachelse}

	<tr>
		<td colspan="8">
				暂无更多
		</td>
	</tr>
	{/foreach}	
</tbody>