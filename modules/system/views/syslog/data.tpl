<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="log" rel="{$row.id}">
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>
		<td>{$row.create_time|date_format:'Y-m-d H:i:s'}</td>			
		<td class="hidden-mobile hidden-tablet">{$row.nickname}</td>	
		<td class="hidden-mobile hidden-tablet">{$row.ip}</td>
		<td>{$types[$row.activity]}</td>
		<td><div>{$row.meta}</div></td>	
	</tr>
	{foreachelse}
	<tr>
		<td colspan="6">无活动日志</td>
	</tr>
	{/foreach}
</tbody>