<tbody data-total="{$total}">
{foreach $results as $c}
    <tr rel="{$c.cid}" name="taoke">
        <td></td>
        <td><input type="checkbox" value="{$c.cid}" class="grp"/></td>
    <td>
        <p>
           <a href="{$c|url}" target="_blank"> <img src="{$c.image|media}" width="80" height="80" style="float: left;margin-right: 5px;">{$c.title}</a>
            {if $c.flag_c}<span class="label bg-color-orange">推荐</span>&nbsp;{/if}
            {if $c.flag_a}<span class="label bg-color-teal">特荐</span>&nbsp;{/if}
            {if $c.flag_h}<span class="label bg-color-red">热门</span>&nbsp;{/if}
        </p>
    </td>
    {'tbkGoodsTable'|tablerow:$c}
    <td class="text-align-right">
        <a href="{'taoke/share'|app}{$c.cid}" target="ajax" class="btn btn-xs btn-success"><i class="fa fa-share"></i></a>
    </td>
    </tr>
    <tr parent="{$c.cid}">
        <td colspan="2"></td>
        <td colspan="{'tbkGoodsTable'|tablespan:2}">
                <form target="ajax" id="apply-form-{$c.cid}" method="post" action="{'taoke/saveReason'|app}" data-widget="nuiValidate" name="ApplyForm" class="smart-form">
                    <input type="hidden" class="form-control" name="page_id" value="{$c.cid}"/>
                        <div class="row">
                        <section class="col col-8">
                            <label for="reason" class="textarea"><textarea name="reason" placeholder="推荐语" id="reason-{$c.cid}"  rows="3">{$c.reason}</textarea></label>
                        </section>
                        <section class="col col-2">
                            <div class="inline-group">
                                <label class="checkbox">
                                    <input type="checkbox" name="checkbox[]" value="1" {if $c.flag_c} checked {/if}/><i></i>推荐
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" name="checkbox[]" value="2" {if $c.flag_a} checked {/if}/><i></i>特荐
                                </label>
                            </div>
                        </section>
                        <section class="col col-2 text-align-right">
                            <button id="submit-{$c.cid}" class="btn btn-xs btn-primary" type="submit"><i class="fa fa-save"></i></button>
                            <a href="{'taoke/share'|app}{$c.cid}" target="ajax" class="btn btn-xs btn-success"><i class="fa fa-share"></i></a>
                        </section>
                        </div>
                </form>
        </td>
    </tr>
  {foreachelse}
    <tr class="text-center">
        <td colspan="{'tbkGoodsTable'|tablespan:4}">
            暂无数据
        </td>
    </tr>
{/foreach}
</tbody>