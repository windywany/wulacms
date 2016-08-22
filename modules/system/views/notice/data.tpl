<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="notice" rel="{$row.id}">
		<td></td>
		<td><input type="checkbox" value="{$row.id}" class="grp"/></td>
		<td>{$row.id}</td>
		<td>
			{if $canEditNotice}
			<a href="{'system/notice/edit'|app}{$row.id}" 
			target="tag" data-tag="#content">{$row.title}</a></td>
			{else}
			{$row.title}
			{/if}
		<td>{$row.nickname}</td>
		<td>{$row.create_time|date_format:'Y-m-d'}</td>	
		<td>{$row.expire_time|date_format:'Y-m-d'}</td>		
	</tr>
	<tr parent="{$row.id}">
		<td colspan="2"></td>
		<td colspan="5">{$row.message|nl2br}</td>
	</tr>
	{foreachelse}
	<tr>		
		<td colspan="7">暂无通知.</td>
	</tr>
	{/foreach}
</tbody>