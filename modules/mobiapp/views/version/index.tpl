<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-android"></i> 更新列表
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a class="btn btn-primary" id="btn_generate_apks"><i
				class="fa fa-fw fa-download"></i> 生成</a> {if $canAddVer} <a
				href="#{'mobiapp/version/add'|app:0}" class="btn btn-success"><i
				class="fa fa-fw fa-plus-square"></i> 添加</a> {/if} {if $canDelVer} <a
				class="btn btn-danger" href="{'mobiapp/version/del'|app}"
				target="ajax" data-grp="#mobi-ch-table tbody input.grp:checked"
				data-arg="ids" data-warn="请选择要删除的应用版本!" data-confirm="你真的要删除选中的版本吗?"><i
				class="glyphicon glyphicon-trash"></i> 删除 </a> {/if}
		</div>
	</div>
</div>
<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body no-padding">
					<form data-widget="nuiSearchForm" data-for="#mobi-ch-table"
						class="smart-form">
						<fieldset>
							<div class="row">
								<section class="col col-md-2">
									<label class="input" for="uuname"> <input type="hidden"
										data-widget="nuiCombox" style="width: 100%" placeholder="应用名称"
										data-source="{'system/ajax/autocomplete/rest_apps/id/name/ver:mobi'|app}"
										name="app_id" />
									</label>
								</section>

								<section class="col col-md-2">
									<label class="input"> <input type="text" placeholder="应用版本"
										name="version" />
									</label>
								</section>

								<section class="col col-md-2">
									<label class="select"> <select name="os"> {html_options
											options=$osList selected=$type}
									</select> <i></i>
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
				<table id="mobi-ch-table" data-widget="nuiTable" data-auto="true"
					data-source="{'mobiapp/version/data'|app}" data-sort="id,d">
					<thead>
						<tr>
							<th width="30"><input type="checkbox" class="grp" /></th>
							<th width="150">应用名称</th>
							<th class="hidden-xs hidden-sm" width="100">版本名称</th>
							<th class="hidden-xs hidden-sm" width="100">版本号</th>
							<th>应用母包</th>
							<th class="hidden-xs hidden-sm" width="50">系统</th>
							<th class="hidden-xs hidden-sm" width="100">强制更新</th>
							<th class="hidden-xs hidden-sm" width="150">最后修改</th>
							<th width="180" class="text-center">操作</th>
						</tr>
					</thead>
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#mobi-ch-table"
						data-limit="20"></div>
				</div>
			</div>
		</article>
	</div>
	<script type="text/javascript">
		nUI.ajaxCallbacks.updateApkUrl = function(args){
			$('#apk-url-'+args.id).attr('href',args.url).html('复制');
			$('#generate-cdn-'+args.id).attr('href',args.url).html('刷新');
		};
		$('#btn_generate_apks').click(function(){
			$('#mobi-ch-table tbody input:checked').each(function(i,e){
				var id = $(e).val();
				$('#apk-url-'+id).html('生成中...');
				$('#generate-apk-'+id).click();
			});
		});

		$('#btn_generate_cdn').click(function(){
			$('#mobi-ch-table tbody input:checked').each(function(i,e){
				var id = $(e).val();
				$('#apk-cdn-'+id).html('刷新中...');
				$('#generate-cdn-'+id).click();
			});
		});


		$('#mobi-ch-table').on('click','a.apk-cc',function(){
			clipboard.copy({
				  "text/plain": $(this).attr('href')
			}).then(
				  function(){ alert('下载地址已经复制.'); },
				  function(err){ alert('无法复制下载地址:'+err); }
			);
			return false;
		});
	</script>
</section>
