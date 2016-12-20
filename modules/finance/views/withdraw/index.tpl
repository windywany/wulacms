<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-sign-out"></i> 提现记录
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
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
										<input type="text" placeholder="会员ID" name="uid" value="{$uid}"/>
									</label>
								</section>

								<section class="col col-md-3">
									<label class="input">
										<input type="text" placeholder="回执ID" name="transid"/>
									</label>
								</section>

								<section class="col col-md-3">
									<label class="select">
										<select name="status" id="status">
											<option value="" selected="selected">请选择状态</option>
											<option value="0">审请中</option>
											<option value="1">已通过</option>
											<option value="2">已拒绝</option>
											<option value="3">已付款</option>
										</select><i></i>
									</label>
								</section>

								<section class="col col-2">
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
					data-source="{'finance/withdraw/data/'|app}"
					data-sort="id"	
					data-tfoot="true"
					data-tree="true">
					<thead>
						<tr>
							<th width="20" class="hidden-xs hidden-sm"></th>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="60" data-sort="id,d">ID</th>
							<th>会员名(ID)</th>
							<th width="60" class="hidden-xs hidden-sm">提现金额</th>
							<th width="120" class="hidden-xs hidden-sm">提现税率/税费(元）</th>
							<th width="120" class="hidden-xs hidden-sm">人工税率/手续费(元）</th>
							<th width="60" class="hidden-xs hidden-sm">实际支付</th>
							<th width="60" class="hidden-xs hidden-sm">提现平台</th>
							<th width="120" class="hidden-xs hidden-sm">提现帐户/实名</th>
							<th width="70" class="hidden-xs hidden-sm">提现时间</th>
							<th width="40" class="hidden-xs hidden-sm">状态</th>
							<th width="100" class="hidden-xs hidden-sm">审核备注</th>
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
