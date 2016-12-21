<tbody data-total="{$total}">
{foreach $rows as $row}
    <tr name="points" rel="{$row.id}">
        <td><input type="checkbox" value="{$row.id}" class="grp"/></td>
        <td>{$row.create_time|date_format:"Y-m-d H:i"}</td>
        <td>{$row.confirmed|date_format:"Y-m-d H:i"}</td>
        <td>{$row.mname}({$row.mid})</td>
        <td>
            {$row.orderid}
        </td>
        <td>
            {$row.amount}
        </td>
        {'depositTable'|tablerow:$row}
        <td class='text-center'>
            <a href="#{'points/record'|app}{$row.mid}/{$row.type}" class="btn btn-primary btn-xs"><i></i>积分流水</a>
            <a href="#{'points/record'|app}{$row.mid}/{$row.type}" class="btn btn-primary btn-xs"><i></i>金币流水</a>
        </td>
    </tr>
    {foreachelse}
    <tr class="hidden-xs hidden-sm">
        <td></td>
        <td colspan="{'depositTable'|tablespan:6}">暂无记录</td>
    </tr>
{/foreach}
</tbody>
