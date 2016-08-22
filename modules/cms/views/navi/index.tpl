<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-sitemap"></i> {$naviType}			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{if $canAddNavi}
			<button type="button" 
					class="btn btn-labeled btn-success"
					data-url="{'cms/navi/add'|app}{$type}"
					target="tag"
					data-tag="#content"
					>
				<span class="btn-label">
					<i class="glyphicon glyphicon-plus"></i>
				</span>新增
			</button>
			{/if}	
		</div>
	</div>
</div>
<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">
				<table 
					id="navi-table"
					data-widget="nuiTable" 
					data-tree="true"										 
					>
					<thead>
						<tr>					
							<th width="200">菜单项名</th>							
							<th class="hidden-xs hidden-sm">URL</th>			
							<th width="60">显示</th>
							<th width="60">排序</th>							
							<th width="120"></th>
						</tr>
					</thead>
					<tbody>
						{foreach $items as $item}
						<tr data-parent="true" rel="{$item.id}" parent="{$item.upid}">	
							<td>								
								{$item.name}													
							</td>							
							<td class="hidden-xs hidden-sm">{$item.url}</td>
							<td>{if !$item.hidden}<span class="label label-success">是</span>{/if}</td>
							<td><input type="text" value="{$item.sort}" class="navi-menu-item-sort form-control" style="width:50px" maxlength="3" /></td>												
							<td class="text-right">
								{if $canEditNavi}
								<a href="{'cms/navi/edit'|app}{$item.id}" class="btn btn-xs btn-primary"
									target="tag"
									data-tag="#content">
								   <i class="fa fa-pencil-square-o"></i></a>
								{/if}
								{if $canAddNavi}
								<a class="btn btn-success btn-xs"
									href="{'cms/navi/add'|app}{$type}/{$item.id}"
									target="tag"
									data-tag="#content"
									title="添加子菜单项"><i class="glyphicon glyphicon-plus"></i></a>
								{/if}								
								{if $canDeleteNavi}
								<a href="{'cms/navi/del'|app}{$item.id}" 
									class="btn btn-danger btn-xs"
									data-confirm="你真的要删除这个菜单项吗？"
									target="ajax"><i class="fa fa-trash-o"></i></a>
								{/if}
							</td>
						</tr>
						{foreachelse}
						<tr>
							<td colspan="5">
								无菜单项.立即
								{if $canAddNavi}
								<a href="{'cms/navi/add'|app}{$type}" target="tag" data-tag="#content">
									新增菜单项
								</a>
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
<script type="text/javascript">
	$('.navi-menu-item-sort').change(function(){
		var sort = $(this).val();
		if(/^\d?\d?\d$/.test(sort)){
			var id = $(this).parents('tr').attr('rel');
			nUI.ajax("{'cms/navi/csort'|app}",{ 
					element:$(this),
					data:{ id:id,sort:sort },
					blockUI:true,
					type:'POST'
			});	
		}
	});
</script>