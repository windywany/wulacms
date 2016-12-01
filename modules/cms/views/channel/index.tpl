<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-sitemap"></i> {$channelType}			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{if $canAddChannel}
			<a class="btn btn-success"
					href="#{'cms/channel/add'|app:0}{if $type}1{/if}">
					<i class="glyphicon glyphicon-plus"></i> 新增
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
				  	<form data-widget="nuiSearchForm" data-for="#channel-table" class="smart-form">
				  		<fieldset>	
				  			<div class="row">
				  				<section class="col col-6">
				  					<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<input type="text" placeholder="关键词" name="keywords"/>
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
					id="channel-table"
					data-widget="nuiTable"
					data-auto="true"
					data-source="{'cms/channel/data'|app}{if $type}1{/if}"
					{if !$type}
					data-tree="true"
                    data-expend="true"
					{/if}>
					<thead>
						<tr>					
							<th>名称</th>
							<th width="50">封面</th>		
							<th width="120" class="hidden-xs hidden-sm">内容模型</th>			
							<th width="120" class="hidden-xs hidden-sm">识别ID</th>
							<th width="60" class="hidden-xs hidden-sm">排序</th>							
							<th width="80"></th>
						</tr>
					</thead>
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#channel-table" data-limit="1000"></div>
				</div>			
			</div>
		</article>
	</div>
</section>
<div id="update-channel-pageurl-dialog" class="hidden">
	<div class="smart-form">			
		<section>
			<div class="inline-group">
				<label class="checkbox">
					<input type="checkbox" class="update_url"/>
					<i></i>同时更新子栏目页面URL</label>				
			</div>
		</section>
	</div>
</div>
<div id="change-url-progress" class="hidden">
	<div class="panel-body">
		<p>
			<span class="label label-info change-url-tip">正在更新...</span> <span class="txt-color-purple pull-right change-url-pps">0%</span>
		</p>
		<div class="progress">
			<div class="progress progress-striped">
				<div style="width: 0" role="progressbar" class="progress-bar bg-color-green change-url-pp"></div>
			</div>
		</div>
		<div class="note txt-color-red">更新未完成前请不要关闭本窗口!!!</div>
	</div>
</div>
<script type="text/javascript">
	var tipWrapper;
	$('#channel-table').delegate('.ch-item-sort','change',function(){
		var sort = $(this).val();
		if(/^\d?\d?\d$/.test(sort)){
			var id = $(this).parents('tr').attr('rel');
			nUI.ajax("{'cms/channel/csort'|app}",{ 
					element:$(this),
					data:{ id:id,sort:sort },
					blockUI:true,
					type:'POST'
			});	
		}
	});
	$('#channel-table').delegate('.update_page_url','click',function(){
		var me = $(this),ids = me.attr('rel');
		if(!ids){
			return false;
		}
		
		var dg = new nUI.Dialog('update-channel-pageurl-dialogX', '更新页面URL', {
            model : true,
            icon:'fa fa-list',
            theme:'success',            
            content:function(dg){
            	return $('#update-channel-pageurl-dialog').html();
            }
        });
    	dg.openLocal(false,[{
    		text:'确定',
    		cls:'btn-primary',
    		click:function(dialog){
    			var flags =[]; 
    			dialog.find('.update_url:checked').each(function(i,e){
    				flags.push(0);	
    			}); 
    			
    			nUI.ajax("{'cms/channel/updateurl'|app}",{ 
    				element:me, 
    				data:{ id:ids,flags:flags.length } ,
    				callback: function(data){
        				if(data.success){
        					if(data.total == 0){
        						alert('无页面需要更新URL');
        						nUI.closeDialog('update-channel-pageurl-dialogX');
        						return;
        					}        					
        					tipWrapper = dg.setContent($('#change-url-progress').html());
        					change_page_url(data.start,data.total,data.chs,data.flags);
        				}else{
        					alert(data.msg);
        					nUI.closeDialog('update-channel-pageurl-dialogX');
        				}	
        			}
    			});
    			return false;
    		}
    	},{
    		text:'取消'
    	}]);
    	return false;
	});
	function change_page_url(start,total,chs,flags){
		if(start >= total){
			tipWrapper.find('.change-url-tip').removeClass('label-info').addClass('label-success').text('更新完成!');			
			tipWrapper.find('.change-url-pps').text('已完成:100%');
			tipWrapper.find('.change-url-pp').css('width','100%');
		}else{
			tipWrapper.find('.change-url-tip').text('正在导入('+start+'/'+total+')...');
			var wp = (start/total*100);
			wp   = wp.toFixed(1)+'%';
			tipWrapper.find('.change-url-pps').text('已完成:'+wp);
			tipWrapper.find('.change-url-pp').css('width',wp);
			$.ajax('{"cms/channel/updateurl"|app}',{
				method:'POST',
				data:{
					start:start,
					total:total,
					flags:flags,
					chs:chs
				},
				success:function(data){
					if(data.success){
						change_page_url(data.start,data.total,data.chs,data.flags);
					}else{
						tipWrapper.find('.change-url-tip').removeClass('label-info').addClass('label-danger').text(data.msg?data.msg:'出错啦！');
						tipWrapper.find('.change-url-pp').removeClass('bg-color-green').addClass('bg-color-red');
					}
				},				
				error:function(){
					tipWrapper.find('.change-url-tip').removeClass('label-info').addClass('label-danger').text('出错啦！');
					tipWrapper.find('.change-url-pp').removeClass('bg-color-green').addClass('bg-color-red');
				}				
			},'json');
		}
	}
</script>