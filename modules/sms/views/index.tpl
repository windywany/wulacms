{extends 'dashboard/views/table.tpl'}
{block title} <i class="fa fa-twitter"></i> 短信日志 {/block}
{block toolbar}
    {*<a href="#" class="btn btn-success">你好</a>*}
{/block}
{block table}
    <div class="panel-body no-padding">
        <form data-widget="nuiSearchForm" data-for="#sms-log-table" class="smart-form">
            <fieldset>
                <div class="row">
                    <section class="col col-md-2">
                        <label class="input">
                            <input type="text" placeholder="手机号码" name="phone"/>
                        </label>
                    </section>

                    <section class="col col-2">
                        <label class="input"> <i class="icon-append fa fa-calendar"></i>
                            <input type="text" name="time" placeholder="发送时间" data-range-to="log-to-date" data-widget="nuiDatepicker" id="log-from-date">
                        </label>
                    </section>

                    <section class="col col-2">
                        <button class="btn btn-sm btn-primary" type="submit">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                    </section>

                </div>
            </fieldset>
        </form>
    </div>
    <table id="sms-log-table"
            data-widget="nuiTable"
            data-auto="true"
            data-source="{'sms/data'|app}">
        <thead>
        <tr>
            <td>发送时间</td>
            <td>手机号码</td>
            <td>供应商</td>
            <td>内容</td>
            <td>状态</td>
        </tr>
        </thead>
    </table>
    <div class="panel-footer">
        <div data-widget="nuiPager" data-for="#sms-log-table" data-limit="20"></div>
    </div>
{/block}