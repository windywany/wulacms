<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="points" rel="{$row.id}">
		<td>
		</td>
		<td><input type="checkbox" value="{$row.id}" class="grp"/></td>
		<td>
			{$row.id}
		</td>
		<td>{$row.name}({$row.mid})</td>
		<td>
            {$row.amount}
		</td>

		<td>
			{$row.balance}
		</td>

		<td>
            {if $row.is_outlay==1}
                <a href="javascript:;" class="btn btn-danger btn-xs">
                    支出<i class="fa fa-arrow-right"></i>
                </a>
            {else}
                否
            {/if}
		</td>
        <td>
			{$types[$row.type]}
        </td>

        <td>{$row.subject}</td>
        <td>{$row.note}</td>
		<td>
			{$row.create_time|date_format:"%Y-%m-%d %H:%M"}
		</td>
		<td class='text-center'>
			<a href="javascript:;" class="btn btn-primary btn-xs"><i></i>查看详情</a>
		</td>
	</tr>
	{foreachelse}
	<tr class="hidden-xs hidden-sm">
		<td></td>
		<td colspan="11">暂无记录</td>
	</tr>
	{/foreach}
</tbody>
