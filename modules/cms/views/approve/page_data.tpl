<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="page" rel="{$row.id}">
		<td class="hidden-xs hidden-sm"></td>
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>
		
		<td>			
			<a href="{'cms/approve/approving'|app}{$row.id}" target="_blank">
				{$row.title|truncate:24:'...'}</a>
			{if $row.flag_h}<span class="label bg-color-redLight">头条</span>{/if}
			{if $row.flag_c}<span class="label bg-color-orange">推荐</span>{/if}
			{if $row.flag_a}<span class="label bg-color-teal">特荐</span>{/if}
			{if $row.flag_b}<span class="label bg-color-blueLight">加粗</span>{/if}
			{if $row.flag_j}<span class="label bg-color-greenLight">跳转</span>{/if}
			
		</td>
		<td class="hidden-xs hidden-sm">{$row.channelName}</td>	
		<td class="hidden-xs hidden-sm">{if $row.publish_time}{$row.publish_time|date_format:'Y-m-d H:i'}{/if}</td>	
		<td class="hidden-xs hidden-sm">
			{$row.update_time|date_format:'Y-m-d H:i'}						
		</td>
		<td class="hidden-xs hidden-sm">{$row.uuname}</td>
	</tr>	
	{foreachelse}
	<tr class="hidden-xs hidden-sm">
		<td colspan="7">无结果</td>
	</tr>
	{/foreach}
</tbody>