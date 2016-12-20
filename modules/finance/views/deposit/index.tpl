<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
        <h1 class="txt-color-blueDark">
            <i class="fa fa-fw fa-sign-in"></i> 充值记录 </h1>
    </div>
</div>
<section id="widget-grid">
    <div class="row">
        <article class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body no-padding">
                    <form data-widget="nuiSearchForm" data-for="#page-table" class="smart-form">
                        <fieldset>
                            <div class="row">

                                <section class="col col-md-3">
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

                                <section class="col col-md-3">
                                    <label class="select">
                                        <select name="confirmed" id="confirmed">
                                            <option value="">请选择状态</option>
                                            <option value="1" selected="selected">成功</option>
                                            <option value="2">失败</option>
                                        </select><i></i>
                                    </label>
                                </section>

                                <section class="col col-1">
                                    <button class="btn btn-sm btn-primary" type="submit">
                                        <i class="fa fa-search"></i> 搜索
                                    </button>
                                </section>
                            </div>
                        </fieldset>
                    </form>

                </div>
                <table id="page-table" data-widget="nuiTable" data-auto="true"
                       data-source="{'finance/deposit/data/'|app}" data-sort="id" data-tfoot="true" data-tree="true">
                    <thead>
                    <tr>
                        <th width="20" class="hidden-xs hidden-sm"></th>
                        <th width="30"><input type="checkbox" class="grp"/></th>
                        <th width="60" data-sort="id,d">ID</th>
                        <th>会员名(ID)</th>
                        <th width="120" class="hidden-xs hidden-sm">订单ID</th>
                        <th width="100">充值金额</th>
                        <th width="100" class="hidden-xs hidden-sm">充值平台</th>
                        <th width="100" class="hidden-xs hidden-sm">交易ID</th>
                        <th width="100" class="hidden-xs hidden-sm">充值帐户</th>
                        <th width="100" class="hidden-xs hidden-sm">设备来源</th>
                        <th width="100" class="hidden-xs hidden-sm">创建时间</th>
                        <th width="100" class="hidden-xs hidden-sm">入帐时间</th>
                        <th width="100" class="hidden-xs hidden-sm">充值项目</th>
                        <th width="80" class='text-center'>操作</th>
                    </tr>
                    </thead>
                </table>
                <div class="panel-footer">
                    <div data-widget="nuiPager" data-for="#page-table" data-limit="20"></div>
                </div>
            </div>
        </article>
    </div>

</section>
