<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
        <h1 class="txt-color-blueDark">
            <i class="fa fa-fw fa-picture-o"></i> 淘宝客商品 </h1>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
        <div class="pull-right margin-top-5 margin-bottom-5">
            {if $canAddPage}
                <a class="btn btn-success" href="#{'cms/page/add/page/taoke'|app:0}">
                    <i class="glyphicon glyphicon-plus"></i> 新增
                </a>
            {/if}
            {if $canDelPage}
                <button type="button" class="btn btn-danger" data-url="{'cms/page/del'|app}" target="ajax"
                        data-grp="#page-table tbody input.grp:checked" data-arg="ids" data-warn="请选择要删除的相册!"
                        data-confirm="你真的要删除选中的相册吗?"><i class="glyphicon glyphicon-trash"></i> 删除
                </button>
            {/if}
            <button type="button" class="btn btn-warning" data-url="{'taoke/changec'|app}" target="ajax"
                    data-grp="#page-table tbody input.grp:checked" data-arg="ids" data-warn="请选择要推荐的文章!"
                    data-confirm="你真的要推荐选中的文章吗?"><i class="fa fa-thumbs-up"></i> 推荐
            </button>
            <button type="button" class="btn btn-success" data-url="{'taoke/changea'|app}" target="ajax"
                    data-grp="#page-table tbody input.grp:checked" data-arg="ids" data-warn="请选择要特荐的文章!"
                    data-confirm="你真的要特荐选中的文章吗?"><i class="fa fa-thumbs-o-up"></i> 特荐
            </button>
            <button disabled="disabled" type="button" id="import_excel" class="btn btn-primary"
                    href="{'taoke/import/index'|app}" target="ajax" data-confirm="开始之前请确保最新的EXCEL文件已经上传?">
                <i class="fa fa-cloud-upload"></i>导入
            </button>
        </div>
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
                                <section class="col col-md-4">
                                    <label class="input">
                                        <input type="text" placeholder="商品名" name="title"/>
                                    </label>
                                </section>
                                <section class="col col-md-3">
                                    <label class="input">
                                        <input type="text" placeholder="平台" name="platform"/>
                                    </label>
                                </section>
                                <section class="col col-md-3">
                                    <label class="select">
                                        <select name="status" id="status">
                                            <option value="">所有</option>
                                            <option value="0">推荐</option>
                                            <option value="1">特荐</option>
                                        </select>
                                        <i></i>
                                    </label>
                                </section>
                                <section class="col col-md-2 text-right">
                                    <button class="btn btn-sm btn-primary" type="submit">
                                        <i class="fa fa-search"></i> 搜索
                                    </button>
                                </section>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <table id="page-table" data-widget="nuiTable" data-auto="true" data-source="{'taoke/data'|app}"
                       data-sort="cp.id,d" data-tfoot="true" data-tree="true">
                    <thead>
                    <tr>
                        <th width="25"></th>
                        <th width="30"><input type="checkbox" class="grp"/></th>
                        <th>商品</th>
                        <th width="120" data-sort="tbk.comission,a">价格/佣金</th>
                        <th width="100" data-sort="tbk.coupon_price,a">优惠券价格</th>
                        <th width="90" data-sort="tbk.real_price,a">折后价格</th>
                        <th width="80" data-sort="tbk.rate,a">收入比率</th>
                        <th width="100" data-sort="tbk.coupon_remain,a">优惠券/剩余</th>
                        <th width="100" data-sort="tbk.coupon_start,a">开始时间</th>
                        <th width="100" data-sort="tbk.coupon_stop,a">结束时间</th>
                        <th width="80">操作</th>
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
<script type="text/javascript">
	if (!window.taobaokeCk) {
		window.taobaokeCk    = function () {
			$.get('{"taoke/import/checkstatus"|app}', function (data) {
				if (data.done) {
					clearInterval(window.taobaokeTimer);
					delete window.taobaokeTimer;
					$('#import_excel').removeAttrs('disabled');
					if (data.msg) {
						alert(data.msg);
						$('#page-table').data('reloadObj').reload();
					}
				}
			}, 'json');
		};
		window.taobaokeEvent = function () {
			if (window.taobaokeTimer) {
				clearInterval(window.taobaokeTimer);
				delete window.taobaokeTimer;
			}
		};
	}

	nUI.ajaxCallbacks.setTbkToken = function (arg) {
		var id = arg.id;
		$('#gbtn-' + id).remove();
		$('#tid-' + id).html('淘口令:' + arg.token);
	};

    nUI.ajaxCallbacks.setTbkShare = function (arg) {
        var id = arg.id;
       Copy(arg.word);
    };

    function Copy(str){
        var save = function(e){
            e.clipboardData.setData('text/plain', str);
            e.preventDefault();
        }
        document.addEventListener('copy', save);
        document.execCommand('copy');
        document.removeEventListener('copy',save);
    }

	nUI.ajaxCallbacks.startImport = function (arg) {
		$('#import_excel').attr('disabled', "true");
		if (!window.taobaokeTimer) {
			window.taobaokeTimer = setInterval(window.taobaokeCk, 10000);
		}
	};

	$(window).unbind('unload-container', window.taobaokeEvent);
	$(window).on('unload-container', window.taobaokeEvent);
	window.taobaokeTimer = setInterval(window.taobaokeCk, 5000);
</script>