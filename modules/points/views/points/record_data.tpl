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
        {'pointsRecords'|tablerow:$row:$types}
        <td class='text-center'>
            <a href="javascript:;" class="btn btn-primary btn-xs"><i></i>查看详情</a>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td></td>
        <td colspan="{'pointsRecords'|tablespan:8}">暂无记录</td>
    </tr>
{/foreach}
</tbody>
