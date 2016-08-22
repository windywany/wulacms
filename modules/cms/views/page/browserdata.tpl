<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr>		
		<td title="{$row.title}">[{$row.name}] 
			{if $row.title2}
			<a href="{$row|url}" target="_blank">{$row.title2|truncate:32:'...'}</a>
			{else}
			<a href="{$row|url}" target="_blank">{$row.title|truncate:32:'...'}</a>
			{/if}
		</td>
		<td><input name="ss_grp" type="{if $ss}radio{else}checkbox{/if}" class="grp"
		 data-url = "{$row|url}"
		 data-title="{$row.title}"
		 data-title2="{$row.title2}"
		 data-img="{$row.image}"
		 data-text="{if $row.title2}{$row.title2}{else}{$row.title}{/if}" value="{$row.id}"/></td>
	</tr>
	{foreachelse}
	<tr>
		<td colspan="2">无记录</td>
	</tr>
	{/foreach}
</tbody>