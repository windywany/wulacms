<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
        <h1 class="txt-color-blueDark">
            <i class="fa fa-fw fa-picture-o"></i> 淘宝客商品 </h1>
    </div>
    {if $canEditPage}
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
        <div class="pull-right margin-top-5 margin-bottom-5">
            <button type="button" class="btn btn-warning" data-url="{'taoke/changec'|app}" target="ajax"
                    data-grp="#tbkGoodsTable tbody input.grp:checked" data-arg="ids" data-warn="请选择要推荐的文章!"
                    data-confirm="你真的要推荐选中的文章吗?"><i class="fa fa-thumbs-up"></i> 推荐
            </button>
            <button type="button" class="btn btn-success" data-url="{'taoke/changea'|app}" target="ajax"
                    data-grp="#tbkGoodsTable tbody input.grp:checked" data-arg="ids" data-warn="请选择要特荐的文章!"
                    data-confirm="你真的要特荐选中的文章吗?"><i class="fa fa-thumbs-o-up"></i> 特荐
            </button>
            <button type="button" class="btn btn-warning" data-url="{'taoke/changeh'|app}" target="ajax"
                    data-grp="#tbkGoodsTable tbody input.grp:checked" data-arg="ids" data-warn="请选择要特荐的文章!"
                    data-confirm="你真的要特荐选中的文章吗?"><i class="fa fa-thumbs-o-up"></i> 热门
            </button>
            <button disabled="disabled" type="button" id="import_excel" class="btn btn-primary"
                    href="{'taoke/import'|app}?file=$#image$" target="ajax" data-confirm="开始之前请确保最新的EXCEL文件已经上传?">
                <i class="fa fa-cloud-upload"></i>导入
            </button>
        </div>
    </div>
    {/if}
</div>
<section id="widget-grid" class="hasr">
    <span class="barhr">
        <i class="fa fa-search"></i>
    </span>
    <div class="rightbar">
        <div class="panel panel-default">
            <div class="panel-body">
                <form data-widget="nuiSearchForm" data-for="#tbkGoodsTable" class="smart-form">
                    {if $canEditPage}
                    <fieldset>
                        <section>
                            <label class="label">优惠券文件</label>
                            <label class="input input-file" for="image">
                                <span class="button" id="uploadImg" data-max-file-size="100mb" data-extensions="xls" data-widget="nuiAjaxUploader" for="#image"  data-water="0">
                                    <i class="fa fa-lg fa-cloud-upload"></i>
                                </span>
                                <a for="image" class="button" href="javascript:;" style="display:none"><i class="fa fa-lg fa-eye txt-color-blue"></i></a>
                                <input type="text" name="image" id="image" value=""/>
                            </label>
                        </section>
                    </fieldset>
                    {/if}
                    <fieldset>
                            <section>
                                <label class="input">
                                    <input type="text" placeholder="商品名" name="title"/>
                                </label>
                            </section>
                        <section>
                            <label class="input">
                                <input type="text" placeholder="店铺名称" name="shopname"/>
                            </label>
                        </section>
                            <section>
                                <label class="select">
                                    <select name="status" id="status">
                                        <option value="">所有</option>
                                        <option value="0">推荐</option>
                                        <option value="1">特荐</option>
                                        <option value="2">热门</option>
                                    </select>
                                    <i></i>
                                </label>
                            </section>
                            <section>
                                <label class="input"> <i class="icon-append fa fa-calendar"></i> <input type="text" name="bd" placeholder="导入开始时间" data-range-to="log-to-date" data-widget="nuiDatepicker" id="log-from-date">
                                </label>
                            </section>
                            <section>
                                <label class="input"> <i class="icon-append fa fa-calendar"></i> <input type="text" name="sd" placeholder="导入结束时间" data-range-from="log-from-date" data-widget="nuiDatepicker" id="log-to-date">
                                </label>
                            </section>
                            <section>
                                {channel_tree name=channel type=$is_topic id=channel value=$channel multi=1 placeholder="请选择栏目"}
                            </section>
                            <section class="text-align-right">
                                <button class="btn btn-sm btn-primary" type="submit">
                                    <i class="fa fa-search"></i> 搜索
                                </button>
                            </section>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <article class="col-sm-12">
            <div class="panel panel-default">
                <table id="tbkGoodsTable" data-widget="nuiTable" data-auto="true" data-source="{'taoke/data'|app}"
                       data-sort="cp.id,d" data-tree="true">
                    <thead>
                    <tr>
                        <th width="25"></th>
                        <th width="30"><input type="checkbox" class="grp"/></th>
                        <th data-sort="cp.id,d">商品</th>
                        {'tbkGoodsTable'|tablehead}
                        <th width="40" class="text-align-right">
                            {'tbkGoodsTable'|tableset}
                        </th>
                    </tr>
                    </thead>
                </table>
                <div class="panel-footer">
                    <div data-widget="nuiPager" data-for="#tbkGoodsTable" data-limit="20"></div>
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
		$('#gbtn-' + id).parents('td').html(arg.token);
	};

    nUI.ajaxCallbacks.setTbkShare = function (arg) {
        var id = arg.id;
        var token = arg.token;
        if(token){
			$('#gbtn-' + id).parents('td').html(arg.token);
        }
        $('#reason-'+id).val(arg.word);
        $('#submit-'+id).attr('disabled',true);
		var hd = $('#submit-'+id).parents('tr').find('.tt-folder');
		if(!hd.hasClass('node-open')){
			hd.click();
		}
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
	window.taobaokeTimer = setInterval(window.taobaokeCk, 1000);
</script>