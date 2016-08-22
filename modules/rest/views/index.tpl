<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-anchor"></i> 接入应用管理			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<button type="button" 
					class="btn btn-labeled btn-success"
					data-url="{'rest/app/add'|app}"
					target="tag"
					data-tag="#content"
					>
				<span class="btn-label">
					<i class="glyphicon glyphicon-plus"></i>
				</span>新增
			</button>			
		</div>
	</div>
</div>
<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body no-padding">			  
				  	<form data-widget="nuiSearchForm" data-for="#restapp-table" class="smart-form">
				  		<fieldset>
				  			<div class="row">				  				
				  				<section class="col col-3">
									<label class="input">										
										<input type="text" placeholder="应用名" name="name"/>
									</label>
								</section>	
								<section class="col col-3">
									<label class="input">										
										<input type="text" placeholder="应用ID" name="appkey"/>
									</label>
								</section>								
								<section class="col col-3">
									<label class="input">										
										<input type="text" placeholder="应用安全码" name="appsecret"/>
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
					id="restapp-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'rest/app/data'|app}"
					data-sort="id,d"	
					data-tfoot="true"
					>
					<thead>
						<tr>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="160" data-sort="name,d">应用名</th>
							<th width="200" data-sort="appkey,d">应用ID</th>
							<th width="200" data-sort="appsecret,d">应用安全码</th>	
							<th>URL</th>
							<th width="85"></th>							
						</tr>
					</thead>					
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#restapp-table" data-limit="10"></div>
				</div>
			</div>
		</article>
	</div>
</section>