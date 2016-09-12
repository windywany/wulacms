<tbody data-total="{$total}">
    {foreach $rows as $row}
        <tr>
            <td>{date('Y-m-d H:i:s',$row.create_time)}</td>
            <td>{$row.phone}</td>
            <td>{$row.vendor}</td>
            <td>{$row.content}</td>
            <td>
                {if $row.status==1}
                    成功
                {else}
                    失败:{$row.note}
                {/if}
            </td>
        </tr>
        {foreachelse}
        <tr ><td rowspan="5">没有相关数据</td></tr>
        {/foreach}
</tbody>