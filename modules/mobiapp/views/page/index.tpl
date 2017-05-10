<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-copy"></i> 移动页面			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{if $canPublish}
			<button type="button" 
					class="btn btn-success"
					data-url="{'mobiapp/page/publish'|app}"
					target="ajax"					
					data-grp="#page-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要发布的页面吗!" 
					data-confirm="你真的要发布选中的页面吗?"
					><i class="fa fa-fw fa-share-square-o"></i> 发布
			</button>
			<button type="button" 
					class="btn btn-warning"
					data-url="{'mobiapp/page/publish'|app}0"
					target="ajax"					
					data-grp="#page-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要撤回的页面吗!" 
					data-confirm="你真的要撤回选中的页面吗?"
					><i class="fa fa-fw fa-sign-in"></i> 撤回
			</button>
			{/if}
			<button type="button" 
					class="btn btn-danger"
					data-url="{'mobiapp/page/del'|app}"
					target="ajax"
					data-grp="#page-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的页面吗!" 
					data-confirm="你真的要删除选中的页面吗?"
					><i class="glyphicon glyphicon-trash"></i> 删除
			</button>			
		</div>		
	</div>
</div>
<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">				
				<div class="panel-body no-padding">
					<ul id="mobi-page-status-tab" class="nav nav-tabs in">	
						<li>
							<a rel="" href="#"><span>全部</span></a>
						</li>					
						<li class="active">
							<a rel="0" class="txt-color-blue" href="#"><span>未发布</span></a>
						</li>
						<li>
							<a rel="1" class="txt-color-green" href="#"><span>已发布</span></a>
						</li>						
					</ul>
				  	<form id="mobi-page-search-form" data-widget="nuiSearchForm" data-for="#page-table" class="smart-form">
				  		<fieldset>
				  			<input type="hidden" value="0" id="status" name="status"/>
				  			<div class="row">
				  				<section class="col col-md-2">
									<label class="input">										
										<input type="text" placeholder="ID" name="pid"/>
									</label>
								</section>
				  				<section class="col col-md-2">
									<label class="input">										
										<input type="text" placeholder="关键词" name="keywords"/>
									</label>
								</section>
								<section class="col col-md-2">
									<label class="select">
										<select name="channel" id="channel">
											{html_options options=$channels selected=$channel}
										</select>
										<i></i>
									</label>
								</section>
								<section class="col col-md-3">
									<label class="input" for="uuname">
									<input type="hidden" 
											data-widget="nuiCombox" 
											style="width:100%"
											placeholder="作者"
											data-source="{'system/ajax/autocomplete/user/user_id/nickname/r:cms'|app}" name="uuname" id="uuname"/>
										</label>
								</section>
								<section class="col col-md-2">
									<label class="select">
										<select name="model" id="model">
											{html_options options=$models}
										</select>
										<i></i>
									</label>
								</section>
								<section class="col col-md-1">
									<button class="btn btn-sm btn-primary" type="submit">
										<i class="fa fa-search"></i>
									</button>
								</section>																			
				  			</div>				  						  					  			
				  		</fieldset>				  		
				  	</form>			  
				</div>
				<table 
					id="page-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'mobiapp/page/data'|app}"					
					data-tfoot="true"
					data-tree="true">
					<thead>
						<tr>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="100">布局样式</th>
							<th class="text-center">预览</th>
							<th width="150">展示模板</th>
							<th width="50">排序</th>
							<th width="80"></th>
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
$('#mobi-page-status-tab a').click(function(){ 
	if($(this).attr('rel') != undefined){
		$('#mobi-page-status-tab li').removeClass('active');
		$(this).parents('li').addClass('active');
		var rel = $(this).attr('rel');
		$('#status').val(rel);
		$('#mobi-page-search-form').submit();
	}
	return false;
});
</script>