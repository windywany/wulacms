<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-9 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-folder-open"></i> {$catelogTitle}			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-3">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{if $canAdd}
			<button type="button" 
					class="btn btn-labeled btn-success"
					data-url="{'cms/catelog/add'|app}{$catelogType}"
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
					id="catelog-table"
					data-widget="nuiTable" 
					data-tree="true"
					data-expend="true"										 
					>
					<thead>
						<tr>					
							<th width="300">分类</th>
							<th width="50">ID</th>
							<th width="120">别名</th>
							<th class="hidden-xs hidden-sm">说明</th>
							<th width="70"></th>
						</tr>
					</thead>
					<tbody>
						{foreach $items as $item}
						<tr data-parent="true" rel="{$item.id}" parent="{$item.upid}">	
							<td>
								<a href="{'cms/catelog/edit'|app}{$item.id}" 
								   target="tag" data-tag="#content">
									{$item.name}
								</a>							
							</td>
							<td>{$item.id}</td>
							<td>{$item.alias}</td>
							<td class="hidden-xs hidden-sm">{$item.note|escape}</td>							
							<td class="text-right">
								{if $canAdd}
								<a href="{'cms/catelog/add'|app}{$catelogType}/{$item.id}" class="btn btn-primary btn-xs" target="tag" data-tag="#content"><i class="glyphicon glyphicon-plus"></i></a>
								{/if}
								{if $canDeleteCatelog}
								<a href="{'cms/catelog/del'|app}{$item.id}" 
									class="btn btn-danger btn-xs"
									data-confirm="你真的要删除这个分类吗？"
									target="ajax"><i class="fa fa-trash-o"></i></a>
								{/if}
							</td>
						</tr>
						{foreachelse}
						<tr>
							<td colspan="5">暂无{$catelogTitle}
								{if $canAdd}，立即
								<a href="{'cms/catelog/add'|app}{$catelogType}" target="tag" data-tag="#content">添加一个{$catelogTitle}</a>.
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