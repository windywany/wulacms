<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
        <h1 class="txt-color-blueDark">
            <i class="fa fa-fw fa-list"></i> 金币流水
        </h1>
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
                                        {if $mid>0 }
                                            <input type="text" placeholder="会员ID" name="mid" value="{$mid}"/>
                                        {else}
                                            <input type="text" placeholder="会员ID" name="mid" value=""/>
                                        {/if}
                                    </label>
                                </section>

                                <section class="col col-md-3">
                                    <label class="select">
                                        <select name="out" id="out">
                                            <option value="" selected="selected">是否支出</option>
                                            <option value="0" {if $row.type==$type}selected{/if}>收入</option>
                                            <option value="1" {if $row.type==$type}selected{/if}>支出</option>
                                        </select><i></i>
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
                <table id="page-table" data-widget="nuiTable" data-auto="true" data-source="{'coins/re_data/'|app}"
                       data-sort="id" data-tfoot="true" data-tree="true">
                    <thead>
                    <tr>
                        <th width="20" class="hidden-xs hidden-sm"></th>
                        <th width="30"><input type="checkbox" class="grp"/></th>
                        <th width="60" data-sort="id,d">ID</th>
                        <th width="200">会员名(ID)</th>
                        <th width="120" class="hidden-xs hidden-sm">金币数量</th>
                        <th width="100" class="hidden-xs hidden-sm">可用金币</th>
                        <th width="100" class="hidden-xs hidden-sm">是否支出</th>
                        <th width="100" class="hidden-xs hidden-sm">金币类型</th>
                        <th width="100" class="hidden-xs hidden-sm">金币项目</th>
                        <th class="hidden-xs hidden-sm">备注</th>
                        <th width="100" class="hidden-xs hidden-sm">创建时间</th>
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
