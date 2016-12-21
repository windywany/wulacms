<tbody data-total="{$total}">
	{foreach $rows as $row}
	<tr name="points" rel="{$row.id}">
		<td><input type="checkbox" value="{$row.id}" class="grp"/></td>
		<td>
            {$row.create_time|date_format:"%Y-%m-%d %H:%M"}
		</td>
		<td>{$row.name}({$row.mid})</td>
		<td>
            {$row.amount}
		</td>
		<td>
            {$types[$row.type]}
		</td>
		<td>
            {if $row.is_outlay==1}
                <a href="javascript:;" class="btn btn-danger btn-xs">
                    支出<i class="fa fa-arrow-right"></i>
                </a>
            {else}
               收入
                <i class="fa fa-arrow-left"></i>
            {/if}
		</td>
        <td>
			{$types[$row.join_type]}
        </td>
		{'coinsRecords'|tablerow:$row}
		<td class='text-center'>
			<a href="javascript:;" class="btn btn-primary btn-xs"><i></i>查看详情</a>
		</td>
	</tr>
	{foreachelse}
	<tr>
		<td></td>
		<td colspan="{'coinsRecords'|tablespan:7}">暂无记录</td>
	</tr>
	{/foreach}
</tbody>
