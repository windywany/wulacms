<tbody data-total="{$total}">
{foreach $results as $c}
    <tr>
     <td><input type="checkbox" value="{{$c.cid}}" class="grp"/></td>
    <td>
        <p> <img src="{$c.image|media}" width="80" height="80" style="float: left;margin-right: 5px;">{$c.title}</p>

    </td>

    <td>{$c.price}/{$c.comission}</td>
    <td>{$c.coupon_price}</td>
    <td>{$c.sale_count}</td>
    <td>{$c.shopname}</td>
    <td>{$c.platform}</td>
    <td>{$c.wangwang}</td>
    <td>{$c.rate}</td>
    <td>{$c.coupon_count}/{$c.coupon_remain}</td>
    <td>{$c.coupon_start}</td>
    <td>{$c.coupon_stop}</td>
    <td>
        <a href="#{'cms/page/edit/page/'|app:0}{$c.cid}" class="btn btn-xs btn-primary">
            <i class="fa fa-pencil-square-o"></i></a>
        <a title="删除" class="btn btn-xs btn-danger"
           href="{'cms/page/del'|app}{$c.cid}" target="ajax"
           data-confirm="你确定要删除这个文章吗?">
            <i class="glyphicon glyphicon-trash"></i></a>
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