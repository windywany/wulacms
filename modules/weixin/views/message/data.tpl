<tbody data-total="{$total}">
{foreach $rows as $row}
    <tr name="user" rel="{$row.id}">
        <td><input type="checkbox" value="{$row.id}" class="grp"/></td>
        <td>
            {$row.id}
        </td>
        <td>
            {$row.name}
        </td>
        <td>
            {$row.keyword}
        </td>
        <td>
            {$row.msgName}
        </td>
        <td>
            {$row.create_time|date_format:"%Y-%m-%d %H:%M"}
        </td>
        <td class="text-center dropdown">
            <a href="#{'weixin/message/edit'|app:0}{$row.id}" class="btn btn-primary btn-xs"> <i
                        class="fa fa-fw fa-edit"></i></a>

            <a data-confirm="你确定要删除这个吗?" target="ajax" href="{'weixin/message/del'|app}{$row.id}"
               class="btn btn-danger btn-xs"> <i class="glyphicon glyphicon-trash"></i></a>

            <a href="#{'weixin/message/info'|app:0}{$row.id}" class="btn btn-primary btn-xs"> <i
                        class="fa fa-fw fa-info"></i></a>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td></td>
        <td colspan="6">暂无关键词回复</td>
    </tr>
{/foreach}
</tbody>
