<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-picture-o"></i> 淘宝客列表
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{if $canAddPage}
			<a class="btn btn-success" href="#{'cms/page/add/page/taoke'|app:0}">
					<i class="glyphicon glyphicon-plus"></i> 新增
			</a>
			{/if}
			{if $canDelPage}
			<button type="button" 
					class="btn btn-danger"
					data-url="{'cms/page/del'|app}"
					target="ajax"
					data-grp="#page-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的相册!" 
					data-confirm="你真的要删除选中的相册吗?"
					><i class="glyphicon glyphicon-trash"></i> 删除
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
				  	<form data-widget="nuiSearchForm" data-for="#page-table" class="smart-form">
				  		<fieldset>
				  			<div class="row">
				  				<section class="col col-md-3">
									<label class="input">										
										<input type="text" placeholder="ID" name="pid"/>
									</label>
								</section>
				  				<section class="col col-md-5">
									<label class="input">										
										<input type="text" placeholder="店铺名" name="name"/>
									</label>
								</section>
								<section class="col col-md-2">
									<label class="select">
										<select name="channel" id="channel">
											{html_options options=$channels selected=$channel}
										</select>
										<i></i>
									</label>
								</section>	
								<section class="col col-md-2 text-right">
									<button class="btn btn-sm btn-primary" type="submit">
										<i class="fa fa-search"></i> 搜索
									</button>
								</section>																								
				  			</div>				  			
				  		</fieldset>				  		
				  	</form>			  
				</div>
				<table 
					id="page-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'taoke/data'|app}"
					data-sort="cp.id,d"
					data-tfoot="true"
					data-tree="true">
					<thead>
						<tr>							
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="40">商品</th>
							<th width="100" data-sort="CP.channel,a" class="hidden-xs hidden-sm">商品价格</th>
							<th width="120" data-sort="CP.create_time,d" class="hidden-xs hidden-sm">商品月销量</th>
							<th width="120" data-sort="CP.update_time,d" class="hidden-xs hidden-sm">佣金</th>
							<th width="90" data-sort="CP.channel,a" class="hidden-xs hidden-sm">店铺名称</th>
                            <th width="90" data-sort="CP.channel,a" class="hidden-xs hidden-sm">优惠券总量</th>
                            <th width="90" data-sort="CP.channel,a" class="hidden-xs hidden-sm">优惠卷剩余</th>
                            <th width="90" data-sort="CP.channel,a" class="hidden-xs hidden-sm">开始时间</th>
                            <th width="90" data-sort="CP.channel,a" class="hidden-xs hidden-sm">结束时间</th>
							<th width="70"></th>
						</tr>
					</thead>
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#page-table" data-limit="20"></div>
				</div>			
			</div>
		</article>
	</div>
</section>