<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-sitemap"></i> 金币类型
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
				<a class="btn btn-success" href="#{'coins/type/add'|app:0}">
					<i class="glyphicon glyphicon-plus"></i> 新增
				</a>
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

								<section class="col col-md-3">
									<label class="input">
										<input type="text" placeholder="类型ID" name="pid"/>
									</label>
								</section>
								
								<section class="col col-md-3">
									<label class="input">
										<input type="text" placeholder="类型名称" name="pname"/>
									</label>
								</section>

								<section class="col col-2">
									<button class="btn btn-sm btn-primary" type="submit">
										<i class="fa fa-search"></i> 搜索
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
					data-source="{'coins/type/data'|app}"
					data-sort="id"	
					data-tfoot="true"
					data-tree="true">
					<thead>
						<tr>
							<th width="20" class="hidden-xs hidden-sm"></th>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="60" data-sort="id,d">ID</th>
							<th width="80" class="hidden-xs hidden-sm">类型名称</th>
							<th width="120" class="hidden-xs hidden-sm">类型</th>
							<th width="100" class="hidden-xs hidden-sm">系统预留</th>
							<th class="hidden-xs hidden-sm">备注</th>
							<th width="150" data-sort="create_time,d" class="hidden-xs hidden-sm">时间</th>
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
