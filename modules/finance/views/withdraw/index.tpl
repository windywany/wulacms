<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
        <h1 class="txt-color-blueDark">
            <i class="fa fa-fw fa-sign-out"></i> 提现记录 </h1>
    </div>
</div>
<section id="widget-grid">
    <div class="row">
        <article class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body no-padding">
                    <form data-widget="nuiSearchForm" data-for="#withdrawTable" class="smart-form">
                        <fieldset>
                            <div class="row">

                                <section class="col col-md-3">
                                    <label class="input">
                                        <input type="text" placeholder="会员ID" name="uid" value=""/>
                                    </label>
                                </section>

                                <section class="col col-md-3">
                                    <label class="input">
                                        <input type="text" placeholder="回执ID" name="transid"/>
                                    </label>
                                </section>
                                    <input type="hidden" value="{$status}" id="status" name="status" >
                                <section class="col col-2">
                                    <button class="btn btn-sm btn-primary" type="submit">
                                        <i class="fa fa-search"></i> 搜索
                                    </button>
                                </section>

                            </div>
                        </fieldset>
                    </form>
                </div>
                <table id="withdrawTable" data-widget="nuiTable" data-auto="true"
                       data-source="{'finance/withdraw/data/'|app}" data-sort="id">
                    <thead>
                    <tr>
                        <th width="30"><input type="checkbox" class="grp"/></th>
                        <th width="120">提现时间</th>
                        <th>会员名(ID)</th>
                        <th width="80">提现金额</th>
                        <th width="100">支付</th>
                        <th width="80">平台</th>
                        {'withdrawTable'|tablehead}
                        <th width="60">状态</th>
                        <th width="150" class="text-right">
                            {'withdrawTable'|tableset}
                        </th>
                    </tr>
                    </thead>
                </table>
                <div class="panel-footer">
                    <div data-widget="nuiPager" data-for="#withdrawTable" data-limit="20"></div>
                </div>
            </div>
        </article>
    </div>

</section>
