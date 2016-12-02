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
										<input type="text" placeholder="商品名" name="title"/>
									</label>
								</section>
				  				<section class="col col-md-3">
									<label class="input">										
										<input type="text" placeholder="平台" name="platform"/>
									</label>
								</section>
								<section class="col col-md-3">
                                    <label class="input">
                                        <input type="text" placeholder="旺旺" name="wangwang"/>
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
							<th>商品</th>
							<th width="150" data-sort="tbk.comission,a" class="hidden-xs hidden-sm">价格/佣金</th>
							<th width="100" data-sort="tbk.coupon_price,a" class="hidden-xs hidden-sm">优惠券价格</th>
							<th width="100" data-sort="tbk.sale_count,a" class="hidden-xs hidden-sm">月销量</th>
							<th width="200"  class="hidden-xs hidden-sm">店铺名称</th>
                            <th width="60"  class="hidden-xs hidden-sm">平台</th>
                            <th width="120"  class="hidden-xs hidden-sm">旺旺</th>
                            <th width="90"  data-sort="tbk.rate,a"class="hidden-xs hidden-sm">收入比率</th>
                            <th width="120" data-sort="tbk.coupon_remain,a" class="hidden-xs hidden-sm">优惠券/剩余</th>
                            <th width="100" data-sort="tbk.coupon_start,a" class="hidden-xs hidden-sm">开始时间</th>
                            <th width="100" data-sort="tbk.coupon_stop,a" class="hidden-xs hidden-sm">结束时间</th>
							<th width="80">操作</th>
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