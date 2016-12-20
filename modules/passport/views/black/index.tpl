<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-user txt-color-red"></i> 黑名单
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a type="button" 
					class="btn btn-success"
					href="#{'passport/black/add'|app:0}{$type}"><i class="glyphicon glyphicon-plus"></i> 新增
			</a>
			<button type="button" 
					class="btn btn-labeled btn-danger"
					data-url="{'passport/black/del'|app}"
					target="ajax"					
					data-grp="#member-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的黑名单!" 
					data-confirm="你真的要删除选中的黑名单吗?">
				<span class="btn-label">
					<i class="glyphicon glyphicon-trash"></i>
				</span>删除
			</button>
		</div>
	</div>
</div>
<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body no-padding">	
				  	<form data-widget="nuiSearchForm" id="member-search-form" data-for="#member-table" class="smart-form">
				  		<fieldset>
				  			<div class="row">
				  				<section class="col col-3 input">
				  					<div class="input-group">
										<input type="text" placeholder="请输入关键词" class="form-control" name="keyword"/>
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
					id="member-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'passport/black/data'|app}"
					data-sort="id,d"	
					data-tfoot="true"
					data-tree="true">
					<thead>
						<tr>
							<th width="20"></th>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="90" data-sort="id,d">ID</th>
							<th>昵称</th>
							<th width="150" data-sort="create_time,d">创建时间</th>
							<th width="80" class="text-center">操作</th>
						</tr>
					</thead>								
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#member-table" data-limit="20"></div>
				</div>			
			</div>
		</article>
	</div>
</section>
