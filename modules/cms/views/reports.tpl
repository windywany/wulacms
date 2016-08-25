<div class="row">
	<artice class="col-sm-12">

		<div class="jarviswidget" id="wid-id-cms-report" data-widget-togglebutton="false" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">
			
			<header>
				<span class="widget-icon"> <i class="glyphicon glyphicon-stats txt-color-darken"></i> </span>
				<h2>数据统计</h2>

				<ul class="nav nav-tabs pull-right in" id="myTab">
					<li class="active">
						<a data-toggle="tab" href="#s1"><i class="fa fa-bar-chart-o"></i> <span class="hidden-mobile hidden-tablet">综合统计</span></a>
					</li>
					{'render_cms_data_report_tabs'|fire}					
				</ul>

			</header>

			<!-- widget div-->
			<div class="no-padding">									
				<!-- end widget edit box -->
				<div class="widget-body">
					<!-- content -->
					<div id="myTabContent" class="tab-content">
						<div class="tab-pane fade active in padding-10 no-padding-bottom" id="s1">
							<div class="row no-space">
								<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">	
									<span class="demo-liveupdate-1"> <span class="onoffswitch-title">最近二周新增/发布页面,只显示我的</span> <span class="onoffswitch">
										<input type="checkbox" name="only_show_my" class="onoffswitch-checkbox" id="only_show_my">
										<label class="onoffswitch-label" for="only_show_my"> 
											<span class="onoffswitch-inner" data-swchon-text="ON" data-swchoff-text="OFF"></span> 
											<span class="onoffswitch-switch"></span> </label> </span> </span>
									<div id="updating-chart" class="chart-large txt-color-blue"></div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 show-stats">
									<div class="row">
										<div class="col-xs-6 col-sm-6 col-md-12 col-lg-12"> <span class="text"> 草稿 <span class="pull-right">{$total3}/{$total}</span> </span>
											<div class="progress">
												<div class="progress-bar bg-color-blueDark" style="width: {$totalp3}%;"></div>
											</div> </div>
										<div class="col-xs-6 col-sm-6 col-md-12 col-lg-12"> <span class="text"> 待审核 <span class="pull-right">{$total1}/{$total}</span> </span>
											<div class="progress">
												<div class="progress-bar bg-color-orange" style="width: {$totalp1}%;"></div>
											</div> </div>
										<div class="col-xs-6 col-sm-6 col-md-12 col-lg-12"> <span class="text"> 待发布<span class="pull-right">{$total4}/{$total}</span> </span>
											<div class="progress">
												<div class="progress-bar bg-color-blue" style="width: {$totalp4}%;"></div>
											</div> </div>
										<div class="col-xs-6 col-sm-6 col-md-12 col-lg-12"> <span class="text"> 已发布 <span class="pull-right">{$total2}/{$total}</span> </span>
											<div class="progress">
												<div class="progress-bar bg-color-greenLight" style="width: {$totalp2}%;"></div>
											</div> </div>
										<div class="col-xs-6 col-sm-6 col-md-12 col-lg-12"> <span class="text txt-color-red"> 未通过 <span class="pull-right">{$total0}/{$total}</span> </span>
											<div class="progress">
												<div class="progress-bar bg-color-red" style="width: {$totalp0}%;"></div>
											</div> </div>
									</div>
								</div>
							</div>

							<div class="show-stat-microcharts">
								<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
									<div class="easy-pie-chart txt-color-orangeDark" data-percent="{$pg_total}" data-pie-size="50">
										<span class="percent percent-sign"></span>
									</div>
									<span class="easy-pie-title"> 
									{if $only_show_my}
									<a href="#{'cms/page/my/page'|app:0}">我的文章</a> 														
									{else}
									<a href="#{'cms/page'|app:0}">所有文章</a> 	
									{/if}
									</span>
								</div>
								<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
									<div class="easy-pie-chart txt-color-green" data-percent="{$tp_total}" data-pie-size="50">
										<span class="percent percent-sign"></span>
									</div>
									<span class="easy-pie-title"> 
									{if $only_show_my}
									<a href="#{'cms/page/my/topic'|app:0}">我的专题</a> 
									{else}
									<a href="#{'cms/page/all/topic'|app:0}">所有专题</a> 
									{/if}
									</span>														
								</div>
								<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
									<div class="easy-pie-chart txt-color-blue" data-percent="{$cp_total}" data-pie-size="50">
										<span class="percent percent-sign"></span>
									</div>
									<span class="easy-pie-title"> <a href="#{'cms/cpage'|app:0}">自定义页面</a> </span>														
								</div>
								{'render_model_report'|fire}
							</div>
							<script type="text/javascript">	
							var options = {
								yaxis : {
									min : 0,
									max : {$maxY}
								},
								xaxis : {
									min:0,
									max:13,
									ticks:{$days}
								},
								colors : ['#E24913','#6595b4'],	
								tooltip : true,
								tooltipOpts : {
									content :'%s %y',
									defaultTheme : false
								},
								series : {		
									lines : {
										show : true,
										lineWidth : 1,
										fill : true,
										fillColor : {
											colors : [{
												opacity : 0.1
											}, {
												opacity : 0.15
											}]
										}
									},
									points : {
										show : true
									},
									shadowSize : 0
								},
								grid : {
									show:true,
									hoverable : true,
									clickable : true,
									borderWidth : 0		
								}
							};
							function getNewData() {	
								var res = {$chartDatas};	
								return res;
							}
							function getPubData() {	
								var res = {$chartPDatas};	
								return res;
							}
							var plot = $.plot($("#updating-chart"), [{ data:getNewData(),label:'新增' },{ data:getPubData(),label:'发布' }], options);
							$('input[type="checkbox"]#only_show_my').click(function() {
								if ($(this).prop('checked')) {
									document.cookie="only_show_my=1;expires=Wed; 01 Jan 1970";
								} else {		
									document.cookie="only_show_my=;expires=Wed; 01 Jan 1970";
								}
								$('#refresh').click();
							});
							if(/only_show_my=1/.test(document.cookie)){
								$('input[type="checkbox"]#only_show_my').prop('checked',true);
							}
							</script>
						</div>						
						
						{'render_cms_data_report_charts'|fire}
					</div>					
				</div>
			</div>			
		</div>

	</artice>
</div>
