<tbody data-total="{$total}">
    {foreach $rows as $row}
        <tr rel="{$row.id}" {if $row.note}data-parent="true"{/if}>
            <td>{date('Y-m-d H:i:s',$row.create_time)}</td>
            <td>{$row.phone}</td>
            <td>{$row.vendor}</td>
            <td>{$row.content}</td>
            <td>
                {if $row.status==1}
                    成功
                {else}
                    失败
                {/if}
            </td>
        </tr>
        {if $row.note}
            <tr>
                <td colspan="5">
                    <strong>错误:</strong>{$row.note|escape}
                </td>
            </tr>
        {/if}
        {foreachelse}
        <tr ><td colspan="5">没有相关数据</td></tr>
        {/foreach}
</tbody>