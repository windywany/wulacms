<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="points" rel="{$row.id}">
		<td>
		</td>
		<td><input type="checkbox" value="{$row.id}" class="grp"/></td>
		<td>
			{$row.id}
		</td>
		<td>{$row.name}</td>
		<td>{$row.type}</td>
		<td>
			{if $row.reserved==1}
				是
			{else}
				否
			{/if}
		</td>


		<td>
			{$row.note}
		</td>

		<td>
			{$row.create_time|date_format:"%Y-%m-%d %H:%M"}
		</td>
		<td class='text-center'>
			{if $row.reserved !=1}
				<a   href="#{'points/type/edit'|app}{$row.id}" class="btn btn-primary btn-xs"><i></i>编辑</a>
				<a data-confirm="你确定要进行 删除 操作？" target="ajax" href="{'points/type/del/'|app}{$row.id}" class="btn btn-primary btn-xs"><i></i>删除</a>
            {/if}
		</td>
	</tr>
	{foreachelse}
	<tr class="hidden-xs hidden-sm">
		<td></td>
		<td colspan="11">暂无记录</td>
	</tr>
	{/foreach}
</tbody>
