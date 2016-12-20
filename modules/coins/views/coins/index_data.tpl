<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="coins" rel="{$row.id}">
		<td>
		</td>
		<td><input type="checkbox" value="{$row.id}" class="grp"/></td>
		<td>
			{$row.id}
		</td>
		<td>{$row.mname}({$row.mid})</td>
		<td>
            {$row.amount}
		</td>

		<td>
			{$row.balance}
		</td>

		<td>
            {$row.outlay}
		</td>
        <td>
            {$types[$row.type]}
        </td>
		<td class='text-center'>
			<a   href="#{'coins/record'|app}{$row.mid}/{$row.type}" class="btn btn-primary btn-xs"><i></i>查看详情</a>
		</td>
	</tr>
	{foreachelse}
	<tr class="hidden-xs hidden-sm">
		<td></td>
		<td colspan="11">暂无记录</td>
	</tr>
	{/foreach}
</tbody>
