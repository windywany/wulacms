<tbody data-total="{$total}">
{foreach $results as $c}
    <tr>
     <td><input type="checkbox" value="{{$c.cid}}" class="grp"/></td>
    <td>
       <img src="/{$c.image}" width="50px;" height="50px;">
    </td>

    <td>{$c.price}</td>
    <td>{$c.sale_count}</td>
    <td>{$c.comission}</td>
    <td>{$c.shopname}</td>
    <td>{$c.coupon_count}</td>
    <td>{$c.coupon_remain}</td>
    <td>{$c.coupon_start}</td>
    <td>{$c.coupon_stop}</td>
    <td>
        <a href="#{'cms/page/edit/page/'|app:0}{$c.cid}" class="btn btn-xs btn-primary">
            <i class="fa fa-pencil-square-o"></i></a>
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