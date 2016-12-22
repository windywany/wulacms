<tbody data-total="{$total}">
{foreach $rows as $row}
    <tr name="finance" rel="{$row.id}">
        <td><input type="checkbox" value="{$row.id}" class="grp"/></td>
        <td>{date('Y-m-d H:i',$row.create_time)}</td>
        <td>{$row.mname}({$row.mid})</td>
        <td>{$row.amount}</td>
        <td>{$row.payment}</td>
        <td>{$row.platform}</td>
        {'withdrawTable'|tablerow:$row}
        <td>
            {if $row.status==0}
                申请中
            {elseif $row.status==1}
                已通过
            {elseif $row.status==2}
                已拒绝
            {elseif $row.status==3}
                已付款
            {else}
                状态异常
            {/if}

        </td>
        <td class='text-right' colspan="2">
            <div class="btn-group">

                {if $row.status==0}
                    <a data-confirm="你确定要进行 通过 操作？" target="ajax" href="{'finance/withdraw/change/pass'|app}{$row.id}"
                       class="btn btn-primary btn-xs">
                        <i class="fa fa-check"></i>
                        通过
                    </a>
                {/if}
                {if $row.status==2}
                    {$row.approve_message}
                {/if}
                {if $row.status==0}
                    <button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle">
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a  target="dialog" dialog-width="500" dialog-title="拒绝理由" dialog-model="true"
                               href="{'finance/withdraw/refuse'|app}{$row.id}" >
                                <i class="fa fa-remove text-danger"></i>拒绝</a>
                        </li>
                {/if}
            </div>
            {if $row.status==1 && $row.status !=3}
                <a data-confirm="你确定要进行 付款 操作？" target="ajax" href="{'finance/withdraw/change/pay'|app}{$row.id}"
                   class="btn btn-primary btn-xs"><i class="fa fa-rmb"></i>付款</a>
            {/if}

        </td>
    </tr>
    {foreachelse}
    <tr>
        <td></td>
        <td colspan="{'withdrawTable'|tablespan:7}">暂无记录</td>
    </tr>
{/foreach}
</tbody>
