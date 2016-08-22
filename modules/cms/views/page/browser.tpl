<div class="panel-body no-padding">			  
	<form data-widget="nuiSearchForm" data-for="#page-browser-table" class="form-horizontal">
		<fieldset>
			<div class="form-group">												
				<section class="col-sm-12">					
					<div class="row">
						<div class="col col-sm-4">
							{if $model}
								<input type="text" class="form-control" value="{$models[$model]}" readonly="readonly"/>
								<input type="hidden" name="model" value="{$model}"/>
							{else}
							<select name="model" id="model" class="form-control">
								{html_options options=$models}
							</select>
							{/if}			
						</div>
						<div class="col col-sm-8">
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
<table id="page-browser-table"
	   data-widget="nuiGrid"
	   data-height="200"
	   data-source="{'cms/page/browsedata'|app}?ss={$ss}">
	<thead>
		<tr>
			<th>标题</th>
			<th width="30">
				{if !$ss}<input type="checkbox" class="grp"/>{/if}
			</th>			
		</tr>
	</thead>	
</table>
<div class="panel-footer">
	<div class="row">
		<div class="col col-sm-8">
			<div data-widget="nuiPager" class="left" data-hidden-tip="true" data-for="#page-browser-table" data-limit="10" data-pp="3"></div>
		</div>
		<div class="col col-sm-4 text-right">			
			<a class="btn btn-xs btn-success nui-insert-btn {if $ss}single-select{/if}">选择</a>
			<a class="btn btn-xs btn-primary nui-insert-btn {if $ss}single-select{/if} close-after">确定</a>			
		</div>
	</div>
</div>