<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="media" rel="{$row.id}">
		<td><input type="checkbox" value="{$row.id}" class="grp" /></td>
		<td>
			<a href="{$row.url|media}" target="_blank">{$row.filename}</a>
			<p>{$row.url}</p>
		</td>
		<td>{$types[$row.type]}</td>	
		<td>{$row.nickname}</td>
		<td>{$row.create_time|date_format:'Y-m-d H:i:s'}</td>	
	</tr>
	{foreachelse}
	<tr>
		<td colspan="5" class="text-center">无媒体文件</td>
	</tr>
	{/foreach}
</tbody>