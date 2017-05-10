<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-info fa-sitemap"></i> 广告配置列表
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">		
			{if $canAddAds}
			<a href="#{'mobiapp/ads/add'|app:0}" class="btn btn-success"><i class="fa fa-fw fa-plus-square"></i> 添加</a>			
			{/if}				
			{if $canDelAds}
			<a class="btn btn-danger"
			   href="{'mobiapp/ads/del'|app}"
			   target="ajax"					
					data-grp="#mobi-ch-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的广告配置版本!" 
					data-confirm="你真的要删除选中的广告配置吗?"
					><i class="glyphicon glyphicon-trash"></i> 删除
			</a>
			{/if}
		</div>
	</div>
</div>
<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body no-padding">			  
				  	<form data-widget="nuiSearchForm" data-for="#mobi-ch-table" class="smart-form">
				  		<fieldset>
				  			<div class="row">
												  				
								<section class="col col-md-2">
									<label class="input">										
										<input type="text" placeholder="名称" name="name"/>
									</label>
								</section>
								
								<section class="col col-md-2">
									<label class="select">
										<select name="os">
											{html_options options=$osList selected=$type}
										</select>
										<i></i>
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
					data-source="{'mobiapp/ads/data'|app}"
					data-sort="id,d"						
					>
					<thead>
						<tr>							
							<th width="30"><input type="checkbox" class="grp"/></th>	
							<th>名称</th>
							<th class="hidden-xs hidden-sm" width="50">系统</th>
							<th class="hidden-xs hidden-sm" width="80">横幅</th>
							<th class="hidden-xs hidden-sm" width="80">底部广告</th>
							<th class="hidden-xs hidden-sm" width="80">插屏</th>
							<th class="hidden-xs hidden-sm" width="80">信息流</th>
							<th class="hidden-xs hidden-sm" width="80">点击插屏</th>
							<th class="hidden-xs hidden-sm" width="80">点击概率</th>	
							<th class="hidden-xs hidden-sm" width="150">最后修改</th>
							<th width="180" class="text-center">操作</th>
						</tr>
					</thead>
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#mobi-ch-table" data-limit="20"></div>
				</div>			
			</div>
		</article>
	</div>
</section>
