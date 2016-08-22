<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-8 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-user-md"></i> 角色管理
			<span>&gt; 权限设置</span>
			<span>&gt; {$role_name}</span>		
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-4">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default btn-labeled" href="#{'system/role'|app:0}{$role_type}">
				<span class="btn-label">
					<i class="glyphicon glyphicon-circle-arrow-left"></i>
				</span> 返回
			</a>		
		</div>
	</div>
</div>
<section id="widget-grid">
	<form action="{'system/role/acl'|app}" method="post" id="acl-form" target="ajax">	
	<input type="hidden" name="role_id" value="{$role_id}"/>	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">
				<table 
					id="acl-table"
					data-widget="nuiTable"
					data-source="{'system/role/acldata'|app}?rid={$role_id}" 
					data-tree="true"
					data-auto="true"
					data-leafIcon="fa fa-ticket"
					>
					<thead>
						<tr>					
							<th></th>
							<th width="100" class="text-right">权限</th>							
						</tr>
					</thead>
				</table>			
			</div>
		</article>
		<section class="col-sm-12">
			<a  class="btn btn-default btn-labeled pull-right" href="#{'system/role'|app:0}{$role_type}">
				<span class="btn-label">
					<i class="glyphicon glyphicon-circle-arrow-left"></i>
				</span> 返回
			</a>			
			<button type="submit" class="btn btn-primary pull-right" style="margin-right:15px;">确定</button>
		</section>
	</div>
	</form>
</section>