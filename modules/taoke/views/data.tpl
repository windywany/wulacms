<tbody data-total="{$total}">
{foreach $results as $c}
    <tr rel="{$c.cid}" name="taoke">
        <td></td>
        <td><input type="checkbox" value="{$c.cid}" class="grp"/></td>
    <td>
        <p>
            <img src="{$c.image|media}" width="80" height="80" style="float: left;margin-right: 5px;">{$c.title}
            {if $c.flag_c}<span class="label bg-color-orange">推荐</span>&nbsp;{/if}
            {if $c.flag_a}<span class="label bg-color-teal">特荐</span>&nbsp;{/if}
        </p>
        <p><span id="tid-{$c.cid}" class="label label-primary">{if $c.token}淘口令:{$c.token}{/if}</span>
                {if !$c.token}<a id="gbtn-{$c.cid}" title="淘口令" class="btn btn-xs btn-primary"
                                                 href="{'taoke/createtoken/'|app}{$c.cid}" target="ajax"
                                                 data-confirm="你确定要生成淘口令吗?">
                <i class="fa fa-pencil-square-o"></i>生成淘口令</a>
                {/if}
        </p>
    </td>

    <td>{$c.price}/{$c.comission}</td>
    <td>{$c.coupon_price}</td>
    <td>{$c.real_price}</td>
    <td>{$c.rate}</td>
    <td>{$c.coupon_count}/{$c.coupon_remain}</td>
    <td>{$c.coupon_start}</td>
    <td>{$c.coupon_stop}</td>
    <td class="text-align-right">
        <div class="btn-group">
        <a href="#{'cms/page/edit/page/'|app:0}{$c.cid}" class="btn btn-xs btn-primary">
            <i class="fa fa-pencil-square-o"></i></a>
        <a title="删除" class="btn btn-xs btn-danger"
           href="{'cms/page/del'|app}{$c.cid}" target="ajax"
           data-confirm="你确定要删除这个文章吗?">
            <i class="glyphicon glyphicon-trash"></i></a>
        </div>
    </td>
    </tr>
    <tr parent="{$c.cid}">
        <td colspan="2"></td>
        <td colspan="12">
            <form target="ajax" id="apply-form" method="post" action="{'taoke/taoke/saveReason'|app}" data-widget="nuiValidate" name="ApplyForm" class="smart-form">
                <label class="form-inline"><input type="hidden" class="form-control" name="page_id" value="{$c.cid}"/></label>
                <label class="form-inline">推荐理由：<input type="text" class="form-control" name="reason" value="{$c.reason}" style="width: 350px;"/></label>
                <label class="form-inline">推荐<input type="checkbox" name="checkbox[]" class="form-control" value="1" {if $c.flag_c} checked {/if}/></label>
                <label class="form-inline">特荐<input type="checkbox" name="checkbox[]" class="form-control" value="2" {if $c.flag_a} checked {/if}/></label>
                <label class="form-inline"><button id="submit" class="btn btn-xs btn-primary" type="submit">保存</button></label>
            </form>
        </td>
    </tr>
  {foreachelse}

    <tr class="text-center">
        <td colspan="12">
            暂无数据
        </td>
    </tr>
{/foreach}
</tbody>