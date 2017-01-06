<tbody data-total="{$total}">
{foreach $rows as $row}
    <tr name="points" rel="{$row.id}">
        <td><input type="checkbox" value="{$row.id}" class="grp"/></td>
        <td>{$row.create_time|date_format:"Y-m-d H:i"}</td>
        <td>{$row.nickname}({$row.mid})</td>
        <td>{$row.amount}</td>
        {'depositTable'|tablerow:$row}
        <td>{$row.status|status:$status}</td>
        <td class='text-right'>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td></td>
        <td colspan="{'depositTable'|tablespan:5}">暂无记录</td>
    </tr>
{/foreach}
</tbody>
