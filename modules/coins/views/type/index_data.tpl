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
			{if $row.reserved}
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
		<td class='text-right'>
			{if !$row.reserved}
				<div class="btn-group">
				{if $canEdit}
				<a   href="#{'coins/type/edit'|app:0}{$row.id}" class="btn btn-primary btn-xs"><i class="fa fa-pencil-square-o"></i></a>
				{/if}
				{if $canDel}
					<a data-confirm="你确定要进行 删除 操作？" target="ajax" href="{'coins/type/del'|app}{$row.id}" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a>
				{/if}
				</div>
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
