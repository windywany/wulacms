<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-twitter"></i> 关键词回复
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">	
			<label class="input">
				<a class="btn btn-success" href="#{'weixin/message/edit'|app:0}"><i class="glyphicon glyphicon-plus"></i> 新增</a>
			</label>
			<button type="button"
					class="btn btn-danger"
					data-url="{'weixin/message/dels'|app}"
					target="ajax"
					data-grp="#page-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的记录!" 
					data-confirm="你真的要删除选中的记录吗?"
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
				  	<form data-widget="nuiSearchForm" data-for="#page-table" class="smart-form">
				  		<fieldset>
				  			<div class="row">
								<section class="col col-md-2">
									<label class="input">
										<input type="text" placeholder="关键字" name="keyword"/>
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
					id="page-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'weixin/message/data'|app}"
					data-sort="id"	
					data-tfoot="true">
					<thead>
						<tr>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="60">ID</th>
							<th width="60">名称</th>
							<th>关键词</th>
							<th width="160">回复类型</th>
							<th width="140" data-sort="create_time,d">更新时间</th>
							<th width="80">操作</th>
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
