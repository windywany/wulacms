<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-8 col-lg-9 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-list-alt"></i> 
			<a href="#{'cms/model'|app:0}">内容模型</a>
			<span>&nbsp;&gt;&nbsp;{$modelName}字段管理</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{if $canAddModel}
			<a class="btn btn-labeled btn-success"
					href="#{'cms/modelfield/add'|app:0}{$model}">
				<span class="btn-label">
					<i class="glyphicon glyphicon-plus"></i>
				</span>新增
			</a>
			{/if}
			{if $canDelModel}
			<button type="button" 
					class="btn btn-labeled btn-danger"
					data-url="{'cms/modelfield/del'|app}"
					target="ajax"					
					data-grp="#modelfield-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的自定义字段!" 
					data-confirm="你真的要删除选中的自定义字段吗?">
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
				<table 
					id="modelfield-table"
					data-widget="nuiTable"							
					data-sort="id,d"
					>
					<thead>
						<tr>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="150" data-sort="name,d">字段名</th>
							<th width="250">标题</th>
							<th width="50" class="hidden-xs hidden-sm">必填</th>
							<th width="50" class="hidden-xs hidden-sm">索引</th>
							<th width="50" class="hidden-xs hidden-sm">自存</th>							
							<th class="hidden-xs hidden-sm">组件类型</th>
							<th width="50" class="hidden-xs hidden-sm">组</th>
							<th width="50" class="hidden-xs hidden-sm">宽</th>	
							<th width="50" class="hidden-xs hidden-sm">排序</th>
							<th width="30"></th>							
						</tr>
					</thead>
					<tbody>
						{foreach $items as $item}
						<tr name="field" rel="{$item.id}">
							<td><input type="checkbox" class="grp" value="{$item.id}"/></td>
							<td>
								<a href="#{'cms/modelfield/edit'|app:0}{$item.id}">
									{$item.name}
								</a>							
							</td>
							<td>{$item.label}</td>
							<td class="hidden-xs hidden-sm">{if $item.required}是{/if}</td>							
							<td class="hidden-xs hidden-sm">{if $item.searchable}是{/if}</td>
							<td class="hidden-xs hidden-sm">{if $item.cstore}是{/if}</td>	
							<td class="hidden-xs hidden-sm">{$widgets->getWidgetName($item.type)}</td>
							<td class="hidden-xs hidden-sm">{if $item.group}{$item.group}{/if}</td>
							<td class="hidden-xs hidden-sm">{if $item.col}{$item.col}{/if}</td>
							<td class="hidden-xs hidden-sm">{$item.sort}</td>
							<td class="text-right">
								{if $canDelModel}
								<a href="{'cms/modelfield/del'|app}{$item.id}" 
									class="btn btn-danger btn-xs"
									data-confirm="你真的要删除这个字段吗？"
									target="ajax"><i class="fa fa-trash-o"></i></a>
								{/if}
							</td>						
						</tr>
						{foreachelse}
						<tr>
							<td colspan="11">无自定义字段</td>
						</tr>
						{/foreach}
					</tbody>
				</table>							
			</div>
		</article>
	</div>
</section>