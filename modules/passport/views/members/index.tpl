<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-user"></i> 会员通行证
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{if $canAddMember}
			<a type="button" 
					class="btn btn-success"
					href="#{'passport/members/add'|app:0}{$type}"><i class="glyphicon glyphicon-plus"></i> 新增
			</a>
			{/if}		
			{if $canDelMember}
			<button type="button" 
					class="btn btn-labeled btn-danger"
					data-url="{'passport/members/del'|app}"
					target="ajax"					
					data-grp="#member-table tbody input.grp:checked" 
					data-arg="uids" 
					data-warn="请选择要删除的通行证!" 
					data-confirm="你真的要删除选中的通行证吗?">
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
					<ul class="nav nav-tabs in" id="passport-type-tab">
						<li>
							<a href="#" class="txt-color-gray"><i class="fa fa-user"></i> <span class="hidden-mobile hidden-tablet">全部</span></a>
						</li>
						<li class="active">
							<a href="#1" class="txt-color-green"><i class="fa fa-user"></i> <span class="hidden-mobile hidden-tablet">正常</span></a>
						</li>
						<li>
							<a href="#2" class="txt-color-orange"><i class="fa fa-user"></i> <span class="hidden-mobile hidden-tablet">未激活</span></a>
						</li>
						<li>
							<a href="#0" class="txt-color-red"><i class="fa fa-user"></i> <span class="hidden-mobile hidden-tablet">禁用</span></a>
						</li>
					</ul>
				  	<form data-widget="nuiSearchForm" id="member-search-form" data-for="#member-table" class="smart-form">
				  		<input type="hidden" id="keyword-type" name="ktype" value="username" />
				  		<input type="hidden" id="status" name="M_status" value="{$status}"/>
				  		<fieldset>
				  			<div class="row">
				  				<section class="col col-6 input">
				  					<div class="input-group">
										<div class="input-group-btn">
											<button tabindex="-1" id="keyword-label" class="btn btn-default btn-iptg" type="button">账户</button>
											<button tabindex="-1" data-toggle="dropdown" class="btn btn-iptg btn-default dropdown-toggle" type="button">
												<span class="caret"></span>
											</button>
											<ul role="menu" id="keyword-type-selector" class="dropdown-menu">
												<li><a rel="mid" href="javascript:void(0);">ID</a></li>
												<li><a rel="username" href="javascript:void(0);">账户</a></li>
												<li><a rel="nickname" href="javascript:void(0);">昵称</a></li>
												<li><a rel="email" href="javascript:void(0);">邮件</a></li>
												<li><a rel="phone" href="javascript:void(0);">手机</a></li>
												{if $enable_invation}
												<li><a rel="invite_mid" href="javascript:void(0);">邀请人ID</a></li>
												{/if}
											</ul>
										</div>										
										<input type="text" placeholder="请输入关键词" class="form-control" name="keyword"/>
									</div>									
								</section>	
				  				<section class="col col-2">
									<label class="select">
										<select name="M_group_id">
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
                                <section class="col col-2">
									<button class="btn btn-sm btn-primary" type="submit">
										<i class="fa fa-search"></i> 搜索
									</button>
								</section>
				  			</div>
                            {if $widgets}{$widgets|render}{/if}
				  		</fieldset>				  		
				  	</form>			  
				</div>
				<table 
					id="member-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'passport/members/data'|app}"
					data-sort="M.mid,d"	
					data-tfoot="true"
					data-tree="true">
					<thead>
						<tr>
							<th width="20"></th>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="90" data-sort="M.mid,d">ID</th>
							<th data-sort="M.username,d">账户</th>
							<th width="90" data-sort="M.group_id,d">组(等级)</th>
							<th width="120">联系方式</th>
							{foreach $columns as $col}
								<th width="{$col.width}" {if $col.sort}data-sort="{$col.sort}"{/if}>{$col.name}</th>
							{/foreach}
							<th width="100" data-sort="M.registered,d">注册日期</th>
							<th width="80" data-sort="M.status,d">状态</th>
							<th width="70" class="text-center"></th>
						</tr>
					</thead>								
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#member-table" data-limit="20"></div>
				</div>			
			</div>
		</article>
	</div>
</section>
<script type="text/javascript">
	$('#passport-type-tab a').click(function(){
		$('#status').val($(this).attr('href').replace('#',''));
		$('#member-search-form').submit();
		var p = $(this).parent();
		$('#passport-type-tab li').not(p).removeClass('active');
		p.addClass('active');
		return false;
	});
	$('#keyword-type-selector').find('a').click(function(){
		var rel = $(this).attr('rel'),lb = $(this).text();
		$('#keyword-label').html(lb);
		$('#keyword-type').val(rel);		
	});
</script>