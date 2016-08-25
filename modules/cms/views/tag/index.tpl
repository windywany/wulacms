<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-4 col-lg-8 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-tags"></i> 
			内链库			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-8 col-lg-4">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{if $canAddTag}
			<a class="btn btn-success"
					href="#{'cms/tag/add'|app:0}"><i class="glyphicon glyphicon-plus"></i> 新增
			</a>
			{/if}
			{if $canUpdateDict}
			<a class="btn btn-primary"
					href="{'cms/tag/update_dict'|app}" target="ajax" data-confirm="你确定要更新字典文件吗?"><i class="glyphicon glyphicon-book"></i> 更新字典
			</a>
			{/if}
			{if $canDelTag}
			<button type="button" 
					class="btn btn-danger"
					data-url="{'cms/tag/del'|app}"
					target="ajax"					
					data-grp="#tag-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的内链!" 
					data-confirm="你真的要删除选中的内链吗?"
					><i class="glyphicon glyphicon-trash"></i> 删除
			</button>
			{/if}
		</div>
	</div>
</div>
<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body no-padding">			  
				  	<form data-widget="nuiSearchForm" data-for="#tag-table" class="smart-form">
				  		<fieldset>
				  			<div class="row">
				  				<section class="col col-3">
									<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<input type="text" placeholder="标签" name="tag"/>
									</label>
								</section>								
								<section class="col col-3">
									<button class="btn btn-sm btn-primary" type="submit">
										<i class="fa fa-search"></i> 搜索
									</button>
								</section>
				  			</div>
				  		</fieldset>				  		
				  	</form>			  
				</div>
				<table 
					id="tag-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'cms/tag/data'|app}"
					data-sort="id,d"		 
					>
					<thead>
						<tr>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="150" data-sort="tag,d">标签</th>
							<th class="hidden-xs hidden-sm" width="250" data-sort="title,d">标题</th>
							<th class="hidden-xs hidden-sm">链接</th>							
							<th width="30"></th>							
						</tr>
					</thead>					
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#tag-table" data-limit="20"></div>
				</div>			
			</div>
		</article>
	</div>
</section>