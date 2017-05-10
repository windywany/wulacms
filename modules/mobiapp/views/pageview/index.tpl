<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-columns"></i> 展示模板
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">		
			{if $canAddPv}
			<a href="#{'mobiapp/pageview/add'|app:0}" class="btn btn-success"><i class="fa fa-fw fa-plus-square"></i> 添加</a>			
			{/if}				
			{if $canDelPv}
			<a class="btn btn-danger"
			   href="{'mobiapp/pageview/del'|app}"
			   target="ajax"					
					data-grp="#mobi-pv-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的模板!" 
					data-confirm="你真的要删除选中的模板吗?"
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
				  	<form data-widget="nuiSearchForm" data-for="#mobi-pv-table" class="smart-form">
				  		<fieldset>
				  			<div class="row">				  				
				  				<section class="col col-md-3">
									<label class="input">										
										<input type="text" placeholder="模板名" name="keywords"/>
									</label>
								</section>
								<section class="col col-md-3">
									<label class="input">										
										<input type="text" placeholder="模板编号" name="refid"/>
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
					id="mobi-pv-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'mobiapp/pageview/data'|app}"
					data-sort="PV.id,d"						
					>
					<thead>
						<tr>							
							<th width="30"><input type="checkbox" class="grp"/></th>	
							<th width="250" data-sort="PV.name,d">模板名</th>
							<th width="150" data-sort="PV.refid,d">模板编号</th>	
							<th width="200">模板文件</th>
							<th>可显示的模型</th>
							<th width="150" data-sort="PV.update_time">最后修改</th>
							<th width="80" class="text-center">操作</th>
						</tr>
					</thead>
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#mobi-pv-table" data-limit="20"></div>
				</div>			
			</div>
		</article>
	</div>
</section>
