{extends 'dashboard/views/blank.tpl'}
{block title}
    <i class="fa fa-fw fa-weixin txt-color-green"></i> 版块帖子
{/block}
{block toolbar}
    {if $canDel}
        <a href="{'bbs/forum/add'|app}" target="tag" data-tag="#forum-editor" data-blockUI="false" class="btn btn-danger">
            <i class="fa fa-w fa-trash-o"></i> 添加
        </a>
    {/if}
{/block}
{block widget}
    <div class="col col-sm-3">
        <form style="padding-left: 3px" data-widget="nuiSearchForm" data-for="#forum-thread-table" class="form-horizontal">
            <fieldset>
                <div class="form-group">
                    <div class="input-group input-group-sm">
                        <input type="text" placeholder="版块" name="forum" class="form-control">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </fieldset>
        </form>
        <div class="panel panel-default">
            <div class="panel-body" style="min-height: 500px">
                <table  class="inbox-table"
                        id="forum-thread-table"
                        data-widget="nuiTable"
                        data-tree="true"
                        data-hh="true"
                        data-no-hover="true"
                        data-source="{'bbs/forum/data'|app}">
                    <thead>
                    <tr>
                        <th>版块</th>
                    </tr>
                    </thead>
                    {include '../forum/data.tpl' is_root=1}
                </table>
            </div>
        </div>
    </div>
    <div class="col col-sm-9" id="thread-pannel">
        <p class="lead" id="thread-tip">请从左侧选择一个版块进行查看.</p>
       <div class="panel panel-default hidden" id="thread-grid">
            <div class="panel-body no-padding">
                <form data-widget="nuiSearchForm" id="thread-form" data-for="#thread-table" class="smart-form">
                    <input type="hidden" id="forumid" name="forumid" value=""/>
                    <fieldset>
                        <div class="row">
                          <section class="col col-sm-4">
                              <label for="subject" class="input">
                                  <input type="text" id="subject" name="subject" placeholder="主题关键词"/>
                              </label>
                          </section>
                          <section class="col col-sm-3">
                              <label class="input" for="uuname">
                                  <input type="hidden"
                                         data-widget="nuiCombox"
                                         style="width:100%"
                                         placeholder="作者"
                                         data-source="{'system/ajax/autocomplete/user/user_id/nickname/r:cms'|app}" name="uuname" id="uuname"/>
                              </label>
                          </section>
                          <section class="col col-sm-2">
                              <label class="input">
                                  <i class="icon-append fa fa-calendar"></i>
                                  <input id="log-from-date" data-widget="nuiDatepicker"
                                         data-range-to ="log-to-date"
                                         type="text" placeholder="从..." name="bd"/>
                              </label>
                          </section>
                          <section class="col col-sm-2">
                              <label class="input">
                                  <i class="icon-append fa fa-calendar"></i>
                                  <input id="log-to-date" data-widget="nuiDatepicker"
                                         data-range-from ="log-from-date"
                                         type="text" placeholder="到..." name="sd"/>
                              </label>
                          </section>
                          <section class="col col-sm-1 text-align-right">
                              <button class="btn btn-sm btn-primary" type="submit">
                                  <i class="fa fa-search"></i> 搜索
                              </button>
                          </section>
                        </div>
                    </fieldset>
                </form>
                <table
                        id="thread-table"
                        data-widget="nuiTable"
                        data-source="{'bbs/thread/data'|app}">
                    <thead>
                    <tr>
                        <th width="30"><input type="checkbox" class="grp"/></th>
                        <th>主题</th>
                        <th width="120">作者</th>
                        <th width="120">回复</th>
                        <th width="120">最后发表</th>
                    </tr>
                    </thead>
                </table>
                <div class="panel-footer">
                    <div data-widget="nuiPager" data-for="#thread-table" data-limit="20"></div>
                </div>
            </div>
       </div>
    </div>
{/block}
{block js}
    <script type="text/javascript">
        $('#forum-thread-table').on('click','.forumname label',function () {
            var me = $(this);
            if(me.hasClass('label-primary')){
                return;
            }
            $('#forum-thread-table').find('.forumname label').not(me).removeClass('label-primary');
            me.addClass('label-primary');
            $('#thread-tip').hide();
            $('#thread-grid').removeClass('hidden');

            $('#forumid').val($(this).data('id'));
            $('#thread-form').submit();
        });
    </script>
{/block}