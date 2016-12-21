<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="finance" rel="{$row.id}">
		<td><input type="checkbox" value="{$row.id}" class="grp"/></td>
        <td>{date('Y-m-d H:i',$row.create_time)}</td>
		<td>{$row.mname}({$row.mid})</td>
		<td>{$row.amount}</td>
		<td>{$row.payment}</td>
		<td>{$row.platform}</td>
        {'withdrawTable'|tablerow:$row}
		<td>
            {if $row.status==0}
				申请中
            {elseif $row.status==1}
				已通过
            {elseif $row.status==2}
				已拒绝
            {elseif $row.status==3}
				已付款
            {else}
				状态异常
            {/if}

		</td>
		<td class='text-center hide'>
            {if 0}
                {if $row.status==0}
                    <a data-confirm="你确定要进行 通过 操作？" target="ajax" href="{'finance/withdraw/change/pass'|app}{$row.id}" class="btn btn-primary btn-xs"><i></i>通过</a>
                    <a data-confirm="你确定要进行 拒绝 操作？" target="ajax" href="{'finance/withdraw/change/refuse'|app}{$row.id}" class="btn btn-primary btn-xs"><i></i>拒绝</a>
                {/if}
            {if $row.status==2}
                <a data-confirm="你确定要进行 未实名操作？" target="ajax" href="{'finance/withdraw/change/rename'|app}{$row.id}" class="btn btn-danger btn-xs"><i></i>未实名</a>
                <a data-confirm="你确定要进行 无openid操作？" target="ajax" href="{'finance/withdraw/change/reopenid'|app}{$row.id}" class="btn btn-danger btn-xs"><i></i>openid异常</a>
            {/if}
				{if $row.status==1 && $row.status !=3}
                    <a data-confirm="你确定要进行 付款 操作？" target="ajax" href="{'finance/withdraw/change/pay'|app}{$row.id}" class="btn btn-primary btn-xs"><i></i>付款</a>
                {/if}

            {/if}

		</td>
	</tr>
	{foreachelse}
	<tr>
		<td></td>
		<td colspan="{'withdrawTable'|tablespan:7}">暂无记录</td>
	</tr>
	{/foreach}
</tbody>
