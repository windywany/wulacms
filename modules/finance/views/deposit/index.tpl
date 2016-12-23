<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
        <h1 class="txt-color-blueDark">
            <i class="fa fa-fw fa-sign-in"></i> 充值记录 </h1>
    </div>
</div>
<section id="widget-grid">
    <div class="row">
        <article class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body no-padding">
                    <form data-widget="nuiSearchForm" data-for="#depositTable" class="smart-form">
                        <fieldset>
                            <div class="row">

                                <section class="col col-md-2">
                                    <label class="input">
                                        <input type="text" placeholder="会员ID" name="uid" value="{$uid}"/>
                                    </label>
                                </section>

                                <section class="col col-md-2">
                                    <label class="input"> <i class="icon-append fa fa-calendar"></i>
                                        <input type="text" name="bd" placeholder="开始时间" data-range-to="log-to-date"
                                               data-widget="nuiDatepicker" id="log-from-date">
                                    </label>
                                </section>
                                <section class="col col-md-2">
                                    <label class="input"> <i class="icon-append fa fa-calendar"></i>
                                        <input type="text" name="sd" placeholder="结束时间" data-range-from="log-from-date"
                                               data-widget="nuiDatepicker" id="log-to-date">
                                    </label>
                                </section>

                                <section class="col col-md-2">
                                    <label class="select">
                                        <select name="confirmed" id="confirmed">
                                            <option value="">请选择状态</option>
                                            <option value="1" selected="selected">已完成</option>
                                            <option value="2">待入帐</option>
                                            <option value="3">待复核</option>
                                            <option value="4">已作废</option>
                                        </select><i></i>
                                    </label>
                                </section>

                                <section class="col col-md-2">
                                    <button class="btn btn-primary btn-sm" type="submit">
                                        <i class="fa fa-search"></i> 搜索
                                    </button>
                                </section>
                            </div>
                        </fieldset>
                    </form>

                </div>
                <table id="depositTable" data-widget="nuiTable" data-auto="true"
                       data-source="{'finance/deposit/data/'|app}" data-sort="id">
                    <thead>
                    <tr>
                        <th width="30"><input type="checkbox" class="grp"/></th>
                        <th width="120">充值时间</th>
                        <th width="120">确认时间</th>
                        <th width="120">入帐时间</th>
                        <th>会员名(ID)</th>
                        <th width="100">充值金额</th>
                        <th width="120">订单ID</th>
                        {'depositTable'|tablehead}
                        <th width="80" class='text-center'>
                            {'depositTable'|tableset}
                        </th>
                    </tr>
                    </thead>
                </table>
                <div class="panel-footer">
                    <div data-widget="nuiPager" data-for="#depositTable" data-limit="20"></div>
                </div>
            </div>
        </article>
    </div>

</section>
