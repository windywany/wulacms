<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-sitemap"></i> 内容栏目
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">		
			{if $canAddCh}
			<a href="#{'mobiapp/channel/add'|app:0}" class="btn btn-success"><i class="fa fa-fw fa-plus-square"></i> 添加</a>			
			{/if}				
			{if $canDelCh}
			<a class="btn btn-danger"
			   href="{'mobiapp/channel/del'|app}"
			   target="ajax"					
					data-grp="#mobi-ch-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的栏目!" 
					data-confirm="你真的要删除选中的栏目吗?"
					><i class="glyphicon glyphicon-trash"></i> 删除
			</a>
			{/if}
		</div>
	</div>
</div>
<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body no-padding">			  
				  	<form data-widget="nuiSearchForm" data-for="#mobi-ch-table" class="smart-form">
				  		<fieldset>
				  			<div class="row">				  				
				  				<section class="col col-md-3">
									<label class="input">										
										<input type="text" placeholder="栏目名" name="keywords"/>
									</label>
								</section>
								<section class="col col-md-3">
									<label class="input">										
										<input type="text" placeholder="栏目编号" name="refid"/>
									</label>
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
					id="mobi-ch-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'mobiapp/channel/data'|app}"
					data-sort="CH.sort,d"						
					>
					<thead>
						<tr>							
							<th width="30"><input type="checkbox" class="grp"/></th>	
							<th width="250">栏目名</th>
							<th width="150">栏目编号</th>	
							<th>绑定的CMS栏目</th>							
							<th width="150">最后修改</th>
							<th width="60">排序</th>
							<th width="80" class="text-center">操作</th>
						</tr>
					</thead>
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#mobi-ch-table" data-limit="20"></div>
				</div>			
			</div>
		</article>
	</div>
</section>
<script type="text/javascript">
	$('#mobi-ch-table').on('change','.ch-item-sort',function(){
		var sort = $(this).val();
		if(/^(0|[1-9]\d?\d?)$/.test(sort)){
			var id = $(this).parents('tr').attr('rel');
			nUI.ajax("{'mobiapp/channel/csort'|app}",{ 
					element:$(this),
					data:{ id:id,sort:sort },
					blockUI:true,
					type:'POST'
			});	
		}
	});
</script>