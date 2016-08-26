<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-volume-up"></i> 公告管理			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{if $canAddNotice}
			<button type="button" 
					class="btn btn-labeled btn-success"
					data-url="{'system/notice/add'|app}"
					target="tag"
					data-tag="#content"
					>
				<span class="btn-label">
					<i class="glyphicon glyphicon-plus"></i>
				</span>新增
			</button>
			{/if}
			{if $canDelNotice}
			<button type="button" 
					class="btn btn-labeled btn-danger"
					data-url="{'system/notice/del'|app}"
					target="ajax"					
					data-grp="#notice-table tbody input.grp:checked" 
					data-arg="uids" 
					data-warn="请选择要删除的通知!" 
					data-confirm="你真的要删除选中的通知吗?"
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
				  	<form data-widget="nuiSearchForm" data-for="#notice-table" class="smart-form">
				  		<input type="hidden" id="keyword-type" name="ktype" value="NT.title" />
				  		<fieldset>
				  			<div class="row">				  				
								<section class="col col-4 input">
				  					<div class="input-group">
										<div class="input-group-btn">
											<button tabindex="-1" id="keyword-label" class="btn btn-default btn-iptg" type="button">标题</button>
											<button tabindex="-1" data-toggle="dropdown" class="btn btn-iptg btn-default dropdown-toggle" type="button">
												<span class="caret"></span>
											</button>
											<ul role="menu" id="keyword-type-selector" class="dropdown-menu">
												<li><a rel="NT.id" href="javascript:void(0);">编号</a></li>
												<li><a rel="NT.title" href="javascript:void(0);">标题</a></li>
												<li><a rel="NT.messages" href="javascript:void(0);">正文</a></li>												
											</ul>
										</div>										
										<input type="text" placeholder="请输入关键词" class="form-control" name="keyword"/>
									</div>									
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
					id="notice-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'system/notice/data'|app}"
					data-sort="notice_id,d"	
					data-tfoot="true"
					data-tree="true"							 
					>
					<thead>
						<tr>
							<th width="20"></th>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="70" data-sort="id,d">ID</th>				
							<th>标题</th>
							<th width="200" data-sort="NT.create_uid,d">发布者</th>
							<th width="120" data-sort="NT.expire_time,d">发布日期</th>
							<th width="120" data-sort="NT.expire_time,d">过期日期</th>														
						</tr>
					</thead>								
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#notice-table" data-limit="20"></div>
				</div>			
			</div>
		</article>
	</div>
</section>
<script type="text/javascript">
	$('#keyword-type-selector').find('a').click(function(){
		var rel = $(this).attr('rel'),lb = $(this).text();
		$('#keyword-label').html(lb);
		$('#keyword-type').val(rel);		
	});
</script>
