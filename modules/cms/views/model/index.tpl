<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-list-alt"></i> 内容模型管理			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{ican res='c:cms/model'}
			<a class="btn btn-labeled btn-success"
					href="#{'cms/model/add'|app:0}0">
				<span class="btn-label">
					<i class="glyphicon glyphicon-plus"></i>
				</span>新增
			</a>			
			{/ican}
		</div>
	</div>
</div>
<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body no-padding">			  
				  	<form data-widget="nuiSearchForm" data-for="#model-table" class="smart-form">
				  		<fieldset>	
				  			<div class="row">
				  				<section class="col col-3">
				  					<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<input type="text" placeholder="关键词" name="keywords"/>
									</label>
				  				</section>		
				  				<section class="col col-4">
				  					<div class="inline-group">
										<label class="radio">
											<input type="radio" name="type" value="-1" checked="checked"/>
											<i></i>全部</label>
										<label class="radio">
											<input type="radio" name="type"  value="0"/>
											<i></i>非专题</label>
										<label class="radio">
											<input type="radio" name="type"  value="1"/>
											<i></i>仅专题</label>
									</div>
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
					id="model-table"
					data-widget="nuiTable" 
					data-auto="true"
					data-source="{'cms/model/data'|app}"
					data-tree="true">
					<thead>
						<tr>
							<th>内容模型名称</th>						
							<th width="120" class="hidden-xs hidden-sm">识别ID</th>							
							<th width="100" class="hidden-xs hidden-sm">专题模型</th>						
							<th width="60" class="hidden-xs hidden-sm">状态</th>
							<th width="100" class="hidden-sm hidden-xs">菜单组</th>
							<th width="80"></th>
						</tr>
					</thead>
				</table>	
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#model-table" data-limit="10"></div>
				</div>		
			</div>
		</article>
	</div>
</section>