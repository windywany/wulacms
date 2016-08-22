<div class="panel-body no-padding">			  
	<form data-widget="nuiSearchForm" data-for="#catalog-browser-table" class="form-horizontal">
		<fieldset>
			<div class="form-group">												
				<section class="col-sm-12">					
					<div class="row">						
						<div class="col col-sm-12">
							<div class="input-group">						
								<input type="text" class="form-control" placeholder="关键词" name="keywords"/>
								<div class="input-group-btn">
									<button type="submit" class="btn btn-primary">
										<i class="fa fa-search"></i>
									</button>									
								</div>
							</div>
						</div>
					</div>					
				</section>					
			</div>
		</fieldset>				  		
	</form>
</div>
<table id="catalog-browser-table"
	   data-widget="nuiGrid"
	   data-height="200"
	   data-source="{'system/catalog/msdata'|app}{$type}?ss={$ss}">
	<thead>
		<tr>
			<th>{$catalogTitle}</th>
			<th width="30">
				{if !$ss}<input type="checkbox" class="grp"/>{/if}
			</th>			
		</tr>
	</thead>	
</table>
<div class="panel-footer">
	<div class="row">
		<div class="col col-sm-8">
			<div data-widget="nuiPager" class="left" data-hidden-tip="true" data-for="#catalog-browser-table" data-limit="10" data-pp="3"></div>
		</div>
		<div class="col col-sm-4 text-right" id="catalog-browser-form">			
			<a class="btn btn-xs btn-success nui-insert-btn {if $ss}single-select{/if}">选择</a>
			<a class="btn btn-xs btn-primary nui-insert-btn {if $ss}single-select{/if} close-after">确定</a>			
		</div>
	</div>	
</div>