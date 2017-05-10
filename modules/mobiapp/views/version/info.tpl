<div class="row">
	<div class="col-xs-12 col-md-6 col-lg-8 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-download"></i> <a href="#{'mobiapp/version'|app:0}">{$name}</a>[V{$version}]渠道列表
		</h1>
	</div>
	<div class="col-xs-12 col-md-6 col-lg-4">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a class="btn btn-primary" id="btn_generate_apks"><i class="fa fa-fw fa-download"></i> 生成APK</a>	
			<a href="#{'mobiapp/version/addinfo'|app:0}{$id}" class="btn btn-success"><i class="fa fa-fw fa-plus-square"></i> 添加</a>
			<a class="btn btn-danger"
			   href="{'mobiapp/version/delinfo'|app}"
			   target="ajax"					
					data-grp="#mobi-ch-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的渠道!" 
					data-confirm="你真的要删除选中的渠道吗?"
					><i class="glyphicon glyphicon-trash"></i> 删除
			</a>
		</div>
	</div>
</div>
<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body no-padding">			  
				  	<form data-widget="nuiSearchForm" data-for="#mobi-ch-table" class="smart-form">
				  		<input type="hidden" name="version_id" value="{$id}"/>
				  		<fieldset>
				  			<div class="row">
								<section class="col col-md-2">
									<label class="input">										
										<input type="text" placeholder="渠道标识" name="market"/>
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
				<table 
					id="mobi-ch-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'mobiapp/version/datainfo'|app}"
					data-sort="id,d"						
					>
					<thead>
						<tr>							
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="150">渠道名称</th>	
							<th width="150">渠道标识</th>
							<th>软件包</th>
							<th width="150">广告配置</th>							
							<th width="150">最后修改</th>
							<th width="80" class="text-center">操作</th>
						</tr>
					</thead>
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#mobi-ch-table" data-limit="20"></div>
				</div>			
			</div>
		</article>
	</div>
	<script type="text/javascript">
		nUI.ajaxCallbacks.updateApkUrl = function(args){
			$('#apk-url-'+args.id).attr('href',args.url).html('复制');
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
