<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-share-square txt-color-orange"></i> 百度手动链接推送
		</h1>
	</div>	
</div>
<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="jarviswidget"
                id="wid-spider-w"     
                data-widget-colorbutton="false"
				data-widget-editbutton="false"
				data-widget-togglebutton="false"
				data-widget-deletebutton="false"
				data-widget-fullscreenbutton="false"
				data-widget-custombutton="false"
				data-widget-collapsed="false"
				data-widget-sortable="false">
                <header>
                     <span class="widget-icon">
                          <i class="fa fa-edit"></i>
                     </span>
                     <h2> 百度链接 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">                          
                          <form name="{$formName}"                          		
                          		data-widget="nuiValidate" action="{'baidutoolkit/bdpush'|app}" 
                          		method="post" id="{$formName}-form" class="smart-form" target="ajax"
                          		>
							<fieldset>							
								{$widgets|render}							
							</fieldset>
							<footer>																
								<button type="submit" class="btn btn-primary" id="start-baba">推送</button>								
							</footer>
						</form>
                     </div>
                     <table data-widget="nuiTable" id="spider-rst-table" class="hidden">
						<thead>
							<tr>																
								<th>URL</th>
								<th width="80">状态</th>
								<th width="80">耗时</th>														
							</tr>
						</thead>
						<tbody id="spider-rst-content">
						</tbody>				
					</table>
                </div>
           </div>
		</article>
	</div>	
</section>
<div id="importdede-progress" class="hidden">
	<p>
		<span class="label label-info" id="import-tip"></span> <span class="txt-color-purple pull-right" id="import-pps">已完成:80%</span>
	</p>
	<div class="progress">
		<div class="progress progress-striped">
			<div style="width: 0" role="progressbar" class="progress-bar bg-color-green" id="import-pp"></div>
		</div>
	</div>
</div>
<script type="text/javascript">	
	nUI.validateRules['{$formName}'] = {$rules};	
	nUI.ajaxCallbacks['startPushProgress'] = function(args){
		$('#importdede-progress').removeClass('hidden');
		$('#btn-import').attr('disabled',true);
		bdpushPage(args.start,args.total);
	};
	function bdpushPage (start,total){
		if(start < total){
			$('#import-tip').text('正在推送('+start+'/'+total+')...');
			var wp = (start/total*100);
			wp   = wp.toFixed(2)+'%';
			$('#import-pps').text('已完成:'+wp);
			$('#import-pp').css('width',wp);
			$.ajax('{"baidutoolkit/bdpush/push"|app}',{
				method:'POST',
				data:{
					start:start,
					total:total
				},
				success:function(data){
					if(data.success){
						bdpushPage(data.start,data.total);
					}else{
						$('#import-tip').removeClass('label-info').addClass('label-danger').text(data.msg?data.msg:'出错啦！服务器未能正确处理推送数据.');
						$('#import-pp').removeClass('bg-color-green').addClass('bg-color-red');
					}
				},				
				error:function(){
					$('#import-tip').removeClass('label-info').addClass('label-danger').text('出错啦！服务器未能正确处理推送数据.');
					$('#import-pp').removeClass('bg-color-green').addClass('bg-color-red');
				}				
			},'json');
		}else{
			$('#import-tip').removeClass('label-info').addClass('label-success').text('推送已经完成!');			
			$('#import-pps').text('已完成:100%');
			$('#import-pp').css('width','100%');
		}
	}	
</script>