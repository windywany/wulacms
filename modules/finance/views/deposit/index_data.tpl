<tbody data-total="{$total}">
{foreach $rows as $row}
    <tr name="points" rel="{$row.id}">
        <td><input type="checkbox" value="{$row.id}" class="grp"/></td>
        <td>{$row.create_time|date_format:"Y-m-d H:i"}</td>
        <td>{$row.confirmed|date_format:"Y-m-d H:i"}</td>
        <td>
            {if $row.order_confirmed}
                {$row.order_confirmed|date_format:"Y-m-d H:i"}
            {else}
                ---
            {/if}
        </td>
        <td>{$row.mname}({$row.mid})</td>
        <td>
            {number_format($row.amount,0,'','')}
        </td>
        <td>
            {$row.orderid}
        </td>
        {'depositTable'|tablerow:$row}
        <td class='text-right'>
        </td>
    </tr>
    {foreachelse}
    <tr class="hidden-xs hidden-sm">
        <td></td>
        <td colspan="{'depositTable'|tablespan:6}">暂无记录</td>
    </tr>
{/foreach}
</tbody>
