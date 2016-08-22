<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-trash-o"></i> 回收站			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">			
			{if $canEmptyRecycle}
			<button type="button" 
					class="btn btn-labeled btn-danger"
					data-url="{'system/recycle/del'|app}"
					target="ajax"					
					data-grp="#recycle-log-table tbody input.grp:checked" 
					data-arg="id" 
					data-warn="请选择要删除的内容!" 
					data-confirm="你真的要删除选中的内容?"
					>
				<span class="btn-label">
					<i class="glyphicon glyphicon-trash"></i>
				</span>删除
			</button>
			{/if}
			{if $canRestoreRecycle}
			<button type="button" 
					class="btn btn-labeled btn-success"
					data-url="{'system/recycle/restore'|app}"
					target="ajax"					
					data-grp="#recycle-log-table tbody input.grp:checked" 
					data-arg="id" 
					data-warn="请选择要还原的内容!" 
					data-confirm="你真的要还原选中的内容?"
					>
				<span class="btn-label">
					<i class="fa fa-fw fa-undo"></i>
				</span>还原
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
				  	<form data-widget="nuiSearchForm" data-for="#recycle-log-table" class="smart-form">
				  		<fieldset>
				  			<div class="row">
				  				<section class="col col-3">
									<label class="input">
										<i class="icon-prepend fa fa-user"></i>
										<i class="icon-append fa fa-search"></i>
										<input type="text" placeholder="用户名" name="user"/>
									</label>
								</section>
								<section class="col col-2">
									<label class="select">										
										<select id="log-type" name="recycle_type">
											{html_options options=$types}
									    </select><i></i>
									</label>
								</section>
								<section class="col col-2">
									<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<i class="icon-append fa fa-calendar"></i>
										<input id="log-from-date" data-widget="nuiDatepicker"
											   data-range-to ="log-to-date"
											   type="text" placeholder="开始时间" name="bd"/>
									</label>
								</section>
								<section class="col col-2">
									<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<i class="icon-append fa fa-calendar"></i>
										<input id="log-to-date" data-widget="nuiDatepicker" 
											   data-range-from ="log-from-date"
											   type="text" placeholder="结束时间" name="sd"/>
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
					id="recycle-log-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'system/recycle/data'|app}"
					data-sort="id,d"	
					data-tfoot="true"
					>
					<thead>
						<tr>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="150" data-sort="recycle_time,d">
								时间
							</th>											
							<th width="150" data-sort="role,d">用户</th>
							<th width="150">内容类型</th>
							<th>详细</th>
							<th width="80"></th>							
						</tr>
					</thead>					
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#recycle-log-table" data-limit="50"></div>
				</div>			
			</div>
		</article>
	</div>
</section>