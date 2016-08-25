<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-list-ul"></i> 
			<a href="#{'cms/block'|app:0}">区块管理</a>
			<span>&gt; {$blockName}</span>
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-8 col-lg-4">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{if $canAddBlock}
			<a type="button" 
					class="btn btn-labeled btn-success"
					href="#{'cms/blockitem/add'|app}{$block}">
				<span class="btn-label">
					<i class="glyphicon glyphicon-plus"></i>
				</span>新增
			</a>
			{/if}
			{if $canDelBlock}
			<button type="button" 
					class="btn btn-labeled btn-danger"
					data-url="{'cms/blockitem/del'|app}"
					target="ajax"					
					data-grp="#blockitem-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的区块内容!" 
					data-confirm="你真的要删除选中的区块内容吗?"
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
				<table 
					id="blockitem-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'cms/blockitem/data'|app}{$block}"
					data-sort="sort,a"		 
					>
					<thead>
						<tr>
							<th width="30"><input type="checkbox" class="grp"/></th>							
							<th width="300">
								标题
							</th>							
							<th width="50">
								图片
							</th>							
							<th width="300">说明</th>
							<th width="40" data-sort="sort,d">排序</th>
							<th width="60"></th>							
						</tr>
					</thead>					
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#blockitem-table" data-limit="20"></div>
				</div>			
			</div>
		</article>
	</div>
</section>
<script type="text/javascript">
	if(!window.changeBlockItemSort){
		window.changeBlockItemSort = function(){
			var sort = $(this).val();
			if(/^\d?\d?\d$/.test(sort)){
				var id = $(this).parents('tr').attr('rel');
				nUI.ajax("{'cms/blockitem/csort'|app}",{ 
						element:$(this),
						data:{ id:id,sort:sort },
						blockUI:true,
						type:'POST'
				});	
			}
		};		
	}
	$('#blockitem-table').off('change','.ch-item-sort',changeBlockItemSort);
	$('#blockitem-table').on('change','.ch-item-sort',changeBlockItemSort);	
</script>