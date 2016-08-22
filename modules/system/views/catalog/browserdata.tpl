<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr>		
		<td > 
			[{$row.alias}] {$row.name}
		</td>
		<td><input name="ss_grp" type="{if $ss}radio{else}checkbox{/if}" class="grp" value="{$row.id}" data-text="{$row.name}"/></td>
	</tr>
	{/foreach}
</tbody>