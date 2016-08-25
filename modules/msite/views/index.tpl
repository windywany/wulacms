<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-globe"></i> 多站点管理			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{if $canMSite}
			<a class="btn btn-labeled btn-success"
					href="#{'msite/add'|app:0}">
				<span class="btn-label">
					<i class="glyphicon glyphicon-plus"></i>
				</span>新增
			</a>
			{/if}
		</div>
	</div>
</div>
<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">				
				<table 
					id="msites-table"
					data-widget="nuiTable"							
					data-sort="id,d"	 
					>
					<thead>
						<tr>
							<th width="20"></th>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="70">ID</th>				
							<th width="150">二级域名</th>
							<th width="100">模板主题</th>
							<th width="200">绑定栏目</th>
							<th>绑定专题栏目</th>							
							<th width="90"></th>							
						</tr>
					</thead>
					<tbody>
						{foreach $sites as $row}
						<tr name="site" rel="{$row.id}">
							<td></td>
							<td><input type="checkbox" value="{$row.id}" class="grp"/></td>
							<td>{$row.id}</td>
							<td>								
								{$row.domain}{if $row.mdomain}({$row.mdomain}){/if}
							</td>
							<td>{$row.theme}{if $row.mtheme}({$row.mtheme}){/if}</td>							
							<td>{$row.channel}</td>
							<td>{$row.topics}</td>							
							<td class="text-right">
								{if $canMSite}
								<a href="#{'msite/edit'|app:0}{$row.id}" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i></a>
								<a href="{'msite/del'|app}{$row.id}" class="btn btn-xs btn-danger" target="ajax" data-confirm="你真的要删除这个站点吗?"><i class="fa fa-trash-o"></i></a>
								{/if}
							</td>		
						</tr>	
						{foreachelse}
						<tr>
							<td colspan="8">
								当前无站点.
								{if $canMSite}
								立即
								<a href="#{'msite/add'|app:0}">
									新增一个
								</a>.
								{/if}
							</td>
						</tr>
						{/foreach}
					</tbody>								
				</table>							
			</div>
		</article>
	</div>
</section>