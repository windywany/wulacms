<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="points" rel="{$row.id}">
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
            {$row.frozen_amount}
		</td>
        <td>
            {date('Y-m-d H:i:s',$row.create_time)}
        </td>
		<td class='text-center'>
			<a   href="#{'finance/deposit/index'|app}{$row.mid}" class="btn btn-primary btn-xs"><i></i>充值详情</a>
			<a   href="#{'finance/withdraw/index'|app}{$row.mid}" class="btn btn-primary btn-xs"><i></i>提现详情</a>
		</td>
	</tr>
	{foreachelse}
	<tr class="hidden-xs hidden-sm">
		<td></td>
		<td colspan="11">暂无记录</td>
	</tr>
	{/foreach}
</tbody>
