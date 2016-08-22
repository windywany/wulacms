<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-user-md"></i> 角色管理			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{if $canAddRole}
			<a  class="btn btn-success" id="add-role-btn"
				href="#{'system/role/add'|app:0}">
				<i class="glyphicon glyphicon-plus"></i> 新增</a>
			{/if}
			{if $canDelRole}
			<button type="button" 
					class="btn btn-labeled btn-danger"
					data-url="{'system/role/del'|app}"
					target="ajax"					
					data-grp="#role-table tbody input.grp:checked" 
					data-arg="rid" 
					data-warn="请选择要删除的角色!" 
					data-confirm="你真的要删除选中的角色吗?"
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
					<ul class="nav nav-tabs in" id="role-type-tab">
						{foreach $types as $t=>$tname}					
						<li {if $type==$t}class="active"{/if}>
							<a href="#" class="txt-color-blue" rel="{$t}"><i class="fa fa-user-md"></i> <span class="hidden-mobile hidden-tablet">{$tname}</span></a>
						</li>
						{/foreach}
					</ul>		  
				  	<form data-widget="nuiSearchForm" id="role-search-form" data-for="#role-table" class="smart-form">
				  		<fieldset>
				  			<input type="hidden" name="type" id="role_type" value="{$type}"/>
				  			<div class="row">
				  				<section class="col col-3">
									<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<input type="text" placeholder="角色名" name="role_name"/>
									</label>
								</section>
								<section class="col col-3">
									<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<input type="text" placeholder="角色" name="role"/>
									</label>
								</section>
								<section class="col col-3">
									<button class="btn btn-sm btn-primary" type="submit">
										<i class="fa fa-search"></i> 搜索
									</button>
								</section>
				  			</div>
				  		</fieldset>				  		
				  	</form>			  
				</div>
				<table 
					id="role-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'system/role/data'|app}"
					data-sort="role_id,d"	
					data-tfoot="true"			 
					>
					<thead>
						<tr>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="70" data-sort="role_id,d">ID</th>					
							<th width="200" data-sort="role_name,d">
								角色名
							</th>						
							<th  class="hidden-xs hidden-sm" width="100" data-sort="role,d">角色</th>							
							<th  class="hidden-xs hidden-sm">说明</th>
							<th width="60">权重</th>
							<th width="30"></th>							
						</tr>
					</thead>					
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#role-table" data-limit="20"></div>
				</div>			
			</div>
		</article>
	</div>
</section>
<script type="text/javascript">
	var add_role_url = $('#add-role-btn').attr('href');
	$('#add-role-btn').attr('href',add_role_url+'{$type}');
	$('.ch-item-sort').change(function(){
		var sort = $(this).val();
		if(/^\d?\d?\d$/.test(sort)){
			var id = $(this).parents('tr').attr('rel');
			nUI.ajax("{'system/role/csort'|app}",{ 
					element:$(this),
					data:{ id:id,priority:sort },
					blockUI:true,
					type:'POST'
			});	
		}
	});
	$('#role-type-tab a').click(function(){ 
		$('#role-type-tab li').removeClass('active');
		$(this).parents('li').addClass('active');
		var rel = $(this).attr('rel');
		$('#role_type').val(rel);
		$('#add-role-btn').attr('href',add_role_url+rel);
		$('#role-search-form').submit();
		return false;
	});
	
</script>
