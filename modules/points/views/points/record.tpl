<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-list"></i> 积分流水
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
										<input type="text" placeholder="会员ID" name="mid" value="{$mid}"/>
									</label>
								</section>

								<section class="col col-md-3">
									<label class="select">
										<select name="type" id="type">
											<option value="" selected="selected">请选择类型</option>
                                            {foreach $types as $row}
                                                <option value="{$row.type}" {if $row.type==$type}selected{/if}>{$row.name}</option>
                                            {/foreach}
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
					data-source="{'points/re_data/'|app}"
					data-sort="id"	
					data-tfoot="true"
					data-tree="true">
					<thead>
						<tr>
							<th width="20" class="hidden-xs hidden-sm"></th>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="60" data-sort="id,d">ID</th>
							<th width="80" class="hidden-xs hidden-sm">会员名(ID)</th>
							<th width="120" class="hidden-xs hidden-sm">积分数量</th>
							<th width="100" class="hidden-xs hidden-sm">可用积分</th>
							<th width="100" class="hidden-xs hidden-sm">是否支出</th>
							<th width="100" class="hidden-xs hidden-sm">积分类型</th>
							<th width="100" class="hidden-xs hidden-sm">是否过期</th>
							<th width="100" class="hidden-xs hidden-sm">过期时间</th>
							<th width="100" class="hidden-xs hidden-sm">积分项目</th>
							<th width="100" class="hidden-xs hidden-sm">备注</th>
							<th width="100" class="hidden-xs hidden-sm">创建时间</th>
							<th width="280" class='text-center'>操作</th>
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
