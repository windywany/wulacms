<tbody data-total="{$total}">
{foreach $rows as $row}
    <tr name="member" rel="{$row.mid}">
        <td></td>
        <td><input type="checkbox" value="{$row.mid}" class="grp"/></td>
        <td>{$row.mid}</td>
        <td>
            <strong>
                {if $canEditMember}
                    <a href="#{'passport/members/edit'|app:0}{$row.mid}">{$row.username}</a>
                {else}
                    {$row.username}
                {/if}
            </strong>
            {if $row.nickname}
                <p><i class="fa fa-user"></i> {$row.nickname}</p>
            {/if}
        </td>
        <td>{$groups[$row.group_id]}</td>
        <td>
            {if $row.email}<p><a href="mailto:{$row.email}">{$row.email}</a></p>{/if}
            {if $row.phone}<p><i class="fa fa-mobile-phone"></i>{$row.phone}</p>{/if}
        </td>
        {foreach $columns as $ck => $col}
            <td>{if $col.render}{$col.render|call_user_func_array:[$row[$ck],$row]}{else}{$row[$ck]}{/if}</td>
        {/foreach}
        <td>
            {$row.registered|date_format:'y-m-d'}
        </td>
        <td>
            {if $row.status == '1'}
                <span class="label label-success">正常</span>
            {elseif $row.status == '2'}
                <span class="label label-warning">未激活</span>
            {elseif $row.status == '3'}
                <span class="label label-primary">待激活</span>
            {else}
                <span class="label label-danger">禁用</span>
            {/if}
        </td>
        <td class="text-right">
            <div class="btn-group">
                {if $canEditMember}
                    <a href="#{'passport/members/edit'|app:0}{$row.mid}" class="btn btn-primary btn-xs"><i
                                class="fa fa-pencil-square-o"></i></a>
                {else}
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle">
                        <i class="fa fa-list"></i>
                    </button>
                {/if}
                <button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right">
                    {'passport_member_actions'|fire:$row}
                    {if $canDelMember}
                        <li><a href="{'passport/members/del'|app}{$row.mid}" data-confirm="你真的要删除吗？" target="ajax"><i
                                        class="fa fa-trash-o text-danger"></i> 删除</a></li>
                    {/if}
                </ul>
            </div>
        </td>
    </tr>
    <tr parent="{$row.mid}">
        <td colspan="2"></td>
        <td colspan="8">
            <p>{if $row.nickname1}推荐人:{$row.nickname1}{/if}</p>
            {'render_member_extra'|fire:$row}
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="10" class="text-center">无记录</td>
    </tr>
{/foreach}
</tbody>