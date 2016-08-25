<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-list-ul"></i> 区块管理			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{if $canAddBlock}
			<a type="button" 
					class="btn btn-labeled btn-success"
					href="#{'cms/block/add'|app:0}">
				<span class="btn-label">
					<i class="glyphicon glyphicon-plus"></i>
				</span>新增
			</a>
			{/if}
			{if $canDelBlock}
			<button type="button" 
					class="btn btn-labeled btn-danger"
					data-url="{'cms/block/del'|app}"
					target="ajax"					
					data-grp="#block-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的区块!" 
					data-confirm="你真的要删除选中的区块吗?"
					>
				<span class="btn-label">
					<i class="glyphicon glyphicon-trash"></i>
				</span>删除
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
				  	<form data-widget="nuiSearchForm" data-for="#block-table" class="smart-form">
				  		<fieldset>
				  			<div class="row">
				  				<section class="col col-3">
									<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<input type="text" placeholder="名称" name="name"/>
									</label>
								</section>
								<section class="col col-3">
									<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<input type="text" placeholder="引用ID" name="refid"/>
									</label>
								</section>							
								<section class="col col-3">
									<label class="select">
										<select name="catelog" id="catelog">
											{html_options options=$options}
										</select>
										<i></i>
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
					id="block-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'cms/block/data'|app}"
					data-sort="CB.id,d"	
					data-tfoot="true"			 
					>
					<thead>
						<tr>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th data-sort="CB.name,d">
								区块名称
							</th>
							<th width="150" data-sort="CB.refid,d">
								引用ID
							</th>
							<th width="150" data-sort="catelog,d">区块分类</th>
							<th width="200">说明</th>
							<th width="150"></th>							
						</tr>
					</thead>					
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#block-table" data-limit="20"></div>
				</div>			
			</div>
		</article>
	</div>
</section>