<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-9 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-database"></i> 数据定义		
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-3">
	{if $canAdd}
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a class="btn btn-success"
					href="#{'system/catatype/add'|app:0}">
					<i class="glyphicon glyphicon-plus"></i> 新增
			</a>			
		</div>
	{/if}
	</div>
</div>
<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body no-padding">			  
				  	<form data-widget="nuiSearchForm" data-for="#catatype-table" class="smart-form">
				  		<fieldset>	
				  			<div class="row">
				  				<section class="col col-6">
				  					<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<input type="text" placeholder="变量名称或标识" name="keywords"/>
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
					id="catatype-table"
					data-widget="nuiTable"
					data-source="{'system/catatype/data'|app}"
					data-sort="type,a"
					data-auto="true">
					<thead>
						<tr>					
							<th width="150" data-sort="name,a">数据名称</th>
							<th width="150" data-sort="type,a">数据标识</th>
							<th width="100" data-sort="is_enum,name">列表数据?</th>
							<th class="hidden-xs hidden-sm">备注</th>
							<th width="80"></th>
						</tr>
					</thead>
				</table>	
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#catatype-table" data-limit="15"></div>
				</div>			
			</div>
		</article>
	</div>
</section>