<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-male"></i> 管理账户			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{if $canAddUser}
			<a  class="btn btn-success"
					href="#{'system/user/add'|app:0}"> <i class="glyphicon glyphicon-plus"></i> 新增</a>
			{/if}
			{if $canDelUser}
			<button type="button" 
					class="btn btn-labeled btn-danger"
					data-url="{'system/user/del'|app}"
					target="ajax"					
					data-grp="#user-table tbody input.grp:checked" 
					data-arg="uids" 
					data-warn="请选择要删除的用户!" 
					data-confirm="你真的要删除选中的用户吗?"
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
				  	<form data-widget="nuiSearchForm" data-for="#user-table" class="smart-form">
				  		<input type="hidden" id="keyword-type" name="ktype" value="username" />
				  		<fieldset>
				  			<div class="row">				  				
								<section class="col col-4 input">
				  					<div class="input-group">
										<div class="input-group-btn">
											<button tabindex="-1" id="keyword-label" class="btn btn-default btn-iptg" type="button">账户</button>
											<button tabindex="-1" data-toggle="dropdown" class="btn btn-iptg btn-default dropdown-toggle" type="button">
												<span class="caret"></span>
											</button>
											<ul role="menu" id="keyword-type-selector" class="dropdown-menu">
												<li><a rel="user_id" href="javascript:void(0);">ID</a></li>
												<li><a rel="username" href="javascript:void(0);">账户</a></li>
												<li><a rel="nickname" href="javascript:void(0);">用户名</a></li>
												<li><a rel="email" href="javascript:void(0);">邮箱</a></li>
											</ul>
										</div>										
										<input type="text" placeholder="请输入关键词" class="form-control" name="keyword"/>
									</div>									
								</section>							
								<section class="col col-2">								
									<label class="select">
										<select name="U.group_id">
											{html_options options=$groups}
										</select>
										<i></i>
									</label>
								</section>							
				  				<section class="col col-2">
									<label class="select">
										<select name="M_role_id">
											{html_options options=$all_roles}
										</select>
										<i></i>
									</label>
								</section>	
				  				<section class="col col-3">
									<div class="inline-group">
										<label class="radio">
											<input type="radio" checked="checked" name="status" value=""/>
											<i></i>全部</label>
										<label class="radio">
											<input type="radio" name="status" value="1"/>
											<i></i>正常</label>
										<label class="radio">
											<input type="radio" name="status" value="0"/>
											<i></i>禁用</label>
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
					id="user-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'system/user/data'|app}"
					data-sort="user_id,d"	
					data-tfoot="true"
					data-tree="true"							 
					>
					<thead>
						<tr>
							<th width="20"></th>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="70" data-sort="user_id,d">ID</th>				
							<th width="150" data-sort="username,d">账户</th>
							<th width="200" data-sort="nickname,d">用户名</th>
							<th width="150" data-sort="U.group_id,d">用户组</th>
							<th>角色</th>
							<th>邮箱</th>
							<th width="60" class="text-center">状态</th>							
						</tr>
					</thead>								
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#user-table" data-limit="20"></div>
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
