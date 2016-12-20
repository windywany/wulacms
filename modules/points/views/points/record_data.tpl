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
            是
            {else}
                否
            {/if}
		</td>
        <td>
			{$types[$row.type]}
        </td>
        <td>
          {if $row.expired==0}
              否
          {else}
              是
          {/if}
        </td>

        <td>{$row.expire_time|date_format:"%Y-%m-%d %H:%M"}</td>

        <td>{$row.subject}</td>
        <td>{$row.note}</td>
		<td>
			{$row.create_time|date_format:"%Y-%m-%d %H:%M"}
		</td>
		<td class='text-center'>
			{*<a   href="#{'points/record'|app}{$row.mid}/{$row.type}" class="btn btn-primary btn-xs"><i></i>查看详情</a>*}
			<a   href="javascript:;" class="btn btn-primary btn-xs"><i></i>查看详情</a>
		</td>
	</tr>
	{foreachelse}
	<tr class="hidden-xs hidden-sm">
		<td></td>
		<td colspan="11">暂无记录</td>
	</tr>
	{/foreach}
</tbody>
