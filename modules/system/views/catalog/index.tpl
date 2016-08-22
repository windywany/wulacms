<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-9 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-folder-open"></i> {$catalogTitle}			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-3">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default" href="#{'system/catatype'|app:0}" >
					<i class="glyphicon glyphicon-circle-arrow-left"></i> 返回
			</a>
			{if $canAddCatalog}
			<a type="button" 
					class="btn btn-success"
					href="#{'system/catalog/add'|app:0}{$catalogType}">
					<i class="glyphicon glyphicon-plus"></i> 添加
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
				  	<form data-widget="nuiSearchForm" data-for="#catalog-table" class="smart-form">
				  		<fieldset>	
				  			<div class="row">
				  				<section class="col col-3">
				  					<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<input type="text" placeholder="关键词" name="keywords"/>
									</label>
				  				</section>	
				  				<section class="col col-3">
				  					<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<input type="text" placeholder="编号" name="alias"/>
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
					id="catalog-table"
					data-widget="nuiTable"
					data-source="{'system/catalog/data'|app}{$catalogType}"
					data-auto="true"
					data-tree="{if !$is_enum}true{/if}">
					<thead>
						<tr>					
							<th width="300" {if $is_enum}data-sort="CT.name,a"{/if}>{$catalogName}</th>
							{if !$hiddenID}
							<th width="90" {if $is_enum}data-sort="CT.id,a"{/if}>ID</th>
							<th width="120" {if $is_enum}data-sort="CT.alias,a"{/if}>编号</th>
							{/if}
							{if $head_col_tpl}{include $head_col_tpl}{/if}
							<th class="hidden-xs hidden-sm">备注</th>
							<th width="70"></th>
						</tr>
					</thead>
				</table>				
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#catalog-table" data-limit="20"></div>
				</div>						
			</div>
		</article>
	</div>
</section>