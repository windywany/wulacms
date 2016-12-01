<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-sitemap"></i> 粉丝管理
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
		
		
			{if $cronStatus ==0}
				<a class="btn btn-success"
						id="weixin-menu-sync"
						href="javascript:;">
						<i class="fa fa-send"></i> 同步粉丝
				</a>
			{else}
				<a class="btn btn-success" disabled="disabled"
						href="javascript:;">
						<i class="fa fa-send"></i> 同步中...
				</a>
			{/if}
			
			{if $canAddChannel}
			<a class="btn btn-success"
					href="#{'weixin/menu/add'|app:0}{if $type}1{/if}">
					<i class="glyphicon glyphicon-plus"></i> 新增
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
				  	<form data-widget="nuiSearchForm" data-for="#channel-table" class="smart-form">
				  		<fieldset>	
				  			<div class="row">
				  				<section class="col col-6">
				  					<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<input type="text" placeholder="关键词" name="keywords"/>
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
					id="channel-table"
					data-widget="nuiTable"
					data-auto="true"
					data-source="{'weixin/fans/data'|app}"
					{if !$type}
					data-tree="true"
					{/if}>
					<thead>
						<tr>					
							<th width="120">头像</th>
							<th>微信账号</th>
							<th width="60" class="hidden-xs hidden-sm">性别</th>
							<th width="60" class="hidden-xs hidden-sm">城市</th>
							<th width="60" class="hidden-xs hidden-sm">省份</th>
							<th width="80" class="hidden-xs hidden-sm">订阅状态</th>
							<th width="180" class="hidden-xs hidden-sm">订阅时间</th>
							<th width="180" class="hidden-xs hidden-sm">更新时间</th>							
							<th width="80"></th>
						</tr>
					</thead>
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#channel-table" data-limit="10"></div>
				</div>			
			</div>
		</article>
	</div>
</section>
<div id="update-channel-pageurl-dialog" class="hidden">
	<div class="smart-form">			
		<section>
			<div class="inline-group">
				<label class="checkbox">
					<input type="checkbox" class="update_url"/>
					<i></i>同时更新子栏目页面URL</label>				
			</div>
		</section>
	</div>
</div>
<div id="change-url-progress" class="hidden">
	<div class="panel-body">
		<p>
			<span class="label label-info change-url-tip">正在更新...</span> <span class="txt-color-purple pull-right change-url-pps">0%</span>
		</p>
		<div class="progress">
			<div class="progress progress-striped">
				<div style="width: 0" role="progressbar" class="progress-bar bg-color-green change-url-pp"></div>
			</div>
		</div>
		<div class="note txt-color-red">更新未完成前请不要关闭本窗口!!!</div>
	</div>
</div>
<script type="text/javascript">
	$('#weixin-menu-sync').click(function(){
		nUI.ajax("{'weixin/fans/sync'|app}",{ 
				blockUI:true,
				type:'POST'
		});	
	});
</script>