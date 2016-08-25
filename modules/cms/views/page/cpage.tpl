<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-copy"></i> 自定义页面			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">			
			{if $enable_approving && $canSubmitPage}
			<input data-widget="nuiDatepicker" style="display:inline-block;width:100px;" class="form-control input-sm" type="text" placeholder="预定时间" id="pubdate"/>
			<input type="text" style="display:inline-block;width:70px;" class="form-control input-sm" data-widget="nuiTimepicker" id="pub_time"/>			
			<a class="btn btn-primary" target="ajax" 
					href="{'cms/approve/submit/cpage-table'|app}?pubdate=$#pubdate$&pubtime=$#pub_time$"
					data-grp="#cpage-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要提交审核的页面!" 
					data-confirm="你真的要将选中的页面提交审核吗?"
					><i class="fa fa-w fa-legal"></i> 送审
			</a>
			{/if}
			{if $canAddPage}
			<a class="btn btn-success" href="#{'cms/cpage/add'|app:0}">				
					<i class="glyphicon glyphicon-plus"></i> 新增
			</a>
			{/if}
			{if $canDelPage}
			<button type="button" 
					class="btn btn-danger"
					data-url="{'cms/page/del'|app}"
					target="ajax"					
					data-grp="#cpage-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的自定义页面!" 
					data-confirm="你真的要删除选中的自定义页面吗?"
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
					<ul class="nav nav-tabs in" id="plugin-type-tab">
						<li {if !$type}class="active"{/if}>
							<a href="#{'cms/cpage'|app:0}" class="txt-color-green"><i class="fa fa-file-text-o"></i> <span class="hidden-mobile hidden-tablet">普通页</span></a>
						</li>
						<li {if $type=='tpl'}class="active"{/if}>
							<a href="#{'cms/cpage'|app:0}tpl/" class="txt-color-blueLight"><i class="fa fa-file-code-o"></i> <span class="hidden-mobile hidden-tablet">模板页</span></a>
						</li>
					</ul>		  
				  	<form data-widget="nuiSearchForm" data-for="#cpage-table" class="smart-form">
				  		<fieldset>
				  			<input type="hidden" name="type" value="{$type}"/>
				  			<div class="row">
				  				<section class="col col-4">
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
											placeholder="请选择作者"
											data-source="{'system/ajax/autocomplete/user/user_id/nickname/r:cms'|app}" name="uuname" id="uuname"/>
										</label>
								</section>
								{if $enable_approving}
								<section class="col col-2">
									<label class="select">
										<select name="status" id="status">
											{html_options options=$status}
										</select>
										<i></i>
									</label>
								</section>
								{/if}	  			
								<section class="col col-md-2 text-right">
									<button class="btn btn-sm btn-primary" type="submit">
										<i class="fa fa-search"></i> 搜索
									</button>
								</section>
				  			</div>				  			
				  		</fieldset>				  		
				  	</form>			  
				</div>
				<table 
					id="cpage-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'cms/cpage/data'|app}"
					data-sort="PG.id,d"	
					data-tfoot="true"
					data-tree="true"			 
					>
					<thead>
						<tr>
							<th width="20" class="hidden-xs hidden-sm"></th>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="60">ID</th>
							<th>页面标题</th>			
							<th width="120">模板</th>
							<th width="120">处理器</th>
							{if $enable_approving}
							<th width="100" data-sort="PG.status,d" class="hidden-xs hidden-sm">状态</th>
							{/if}
							<th width="140" data-sort="PG.update_time,d" class="hidden-xs hidden-sm">最后更新</th>
							<th width="100"></th>							
						</tr>
					</thead>					
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#cpage-table" data-limit="20"></div>
				</div>			
			</div>
		</article>
	</div>
</section>