<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
        <h1 class="txt-color-blueDark">
            <i class="fa fa-fw fa-list"></i> 积分流水 </h1>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
        <div class="pull-right margin-top-5 margin-bottom-5"></div>
    </div>
</div>
<section id="widget-grid">
    <div class="row">
        <article class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body no-padding">
                    <form data-widget="nuiSearchForm" data-for="#pointsRecords" class="smart-form">
                        <fieldset>
                            <div class="row">

                                <section class="col col-md-3">
                                    <label class="input">
                                        <input type="text" placeholder="会员ID" name="mid" value="{$mid}"/>
                                    </label>
                                </section>

                                <section class="col col-md-3">
                                    <label class="select">
                                        <select name="type" id="type">
                                            <option value="" selected="selected">请选择类型</option>
                                            {foreach $types as $row}
                                                <option value="{$row.type}"
                                                        {if $row.type==$type}selected{/if}>{$row.name}</option>
                                            {/foreach}
                                        </select><i></i>
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
                <table id="pointsRecords" data-widget="nuiTable" data-auto="true" data-source="{'points/re_data/'|app}"
                       data-sort="create_time,d">
                    <thead>
                    <tr>
                        <th width="30"><input type="checkbox" class="grp"/></th>
                        <th width="120" data-sort="create_time,d">日期</th>
                        <th>会员名(ID)</th>
                        <th width="100">总积分</th>
                        <th width="100">可用积分</th>
                        <th width="100">支出</th>
                        <th width="100">类型</th>
                        {'pointsRecords'|tablehead}
                        <th width="80" class='text-center'>
                            {'pointsRecords'|tableset}
                        </th>
                    </tr>
                    </thead>
                </table>
                <div class="panel-footer">
                    <div data-widget="nuiPager" data-for="#pointsRecords" data-limit="20"></div>
                </div>
            </div>
        </article>
    </div>

</section>
