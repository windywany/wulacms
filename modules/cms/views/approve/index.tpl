<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-legal"></i> 页面审核			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">		
			{if $canApprove}
			
			<input data-widget="nuiDatepicker" style="display:inline-block;width:100px;" class="form-control input-sm" type="text" placeholder="预定时间" id="pubdate"/>
			<input type="text" style="display:inline-block;width:70px;" class="form-control input-sm" data-widget="nuiTimepicker" id="pub_time"/>
			<a class="btn btn-primary" target="ajax" 
				href="{'cms/approve/approve/2'|app}?pubdate=$#pubdate$&pubtime=$#pub_time$"
				data-grp="#page-table tbody input.grp:checked" 
				data-arg="ids" 
				data-warn="请选择要定时发布的页面!" 
				data-confirm="你真的要将选中的页面定时发布吗?"
				><i class="fa fa-w fa-clock-o"></i> 定时
			</a>
			<a class="btn btn-success" target="ajax" 
					href="{'cms/approve/approve/1'|app}"
					data-grp="#page-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要通过审核的页面!" 
					data-confirm="你真的要将选中的页面通过审核吗?"
					><i class="fa fa-w fa-thumbs-o-up"></i> 通过
			</a>
			<a class="btn btn-danger" target="ajax" 
					href="{'cms/approve/approve/0'|app}"
					data-grp="#page-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要拒绝通过的页面!" 
					data-confirm="你真的要将选中的页面拒绝通过审核吗?"
					> <i class="fa fa-w fa-thumbs-o-down"></i> 拒绝
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
				  	<form data-widget="nuiSearchForm" data-for="#page-table" class="smart-form">
				  		<fieldset>
				  			<div class="row">
				  				<section class="col col-2">
									<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<input type="text" placeholder="关键词" name="keywords"/>
									</label>
								</section>
								<section class="col col-3">
									<label class="input" for="uuname">
									<input type="hidden" 
											data-widget="nuiCombox" 
											style="width:99%"
											data-source="{'system/ajax/autocomplete/user/user_id/nickname/r:cms'|app}" name="uuname" id="uuname"/>
										</label>
								</section>						
								<section class="col col-2">
									<label class="select">
										<select name="channel" id="channel">
											{html_options options=$channels}
										</select>
										<i></i>
									</label>
								</section>		
								<section class="col col-2">
									<label class="select">
										<select name="model" id="model">
											{html_options options=$models}
										</select>
										<i></i>
									</label>
								</section>													
								<section class="col col-2">
									<div class="inline-group"><label class="checkbox"><input type="checkbox" name="willpub"/><i></i>定时待发布</label></div>
								</section>
								<section class="col col-1">
									<button class="btn btn-sm btn-primary" type="submit">
										<i class="fa fa-search"></i>
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
					data-source="{'cms/approve/data'|app}"
					data-sort="PG.update_time,d"	
					data-tfoot="true"
					data-tree="true"			 
					>
					<thead>
						<tr>
							<th width="20" class="hidden-xs hidden-sm"></th>
							<th width="30"><input type="checkbox" class="grp"/></th>							
							<th>标题</th>							
							<th width="160" data-sort="PG.channel,a" class="hidden-xs hidden-sm">栏目</th>							
							<th width="140" data-sort="PG.publish_time,a" class="hidden-xs hidden-sm">定时发布</th>
							<th width="140" data-sort="PG.update_time,d" class="hidden-xs hidden-sm">最后更新</th>
							<th width="140" data-sort="UU.nickname,d" class="hidden-xs hidden-sm">作者</th>												
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