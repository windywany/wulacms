<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-copy"></i> {$pageTitle}			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">	
			{'on_init_pages_toolbar'|fire}	
			{if $disable_approving && $canSubmitPage}
			<input data-widget="nuiDatepicker" style="display:inline-block;width:100px;" class="form-control input-sm" type="text" placeholder="预定时间" id="pubdate"/>
			<input type="text" style="display:inline-block;width:70px;" class="form-control input-sm" data-widget="nuiTimepicker" id="pub_time"/>			
			<a class="btn btn-primary" target="ajax" 
					href="{'cms/approve/submit/page-table'|app}?pubdate=$#pubdate$&pubtime=$#pub_time$"
					data-grp="#page-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要提交审核的页面!" 
					data-confirm="你真的要将选中的页面提交审核吗?"
					><i class="fa fa-w fa-legal"></i> 送审
			</a>
			{/if}
			{if $canEditTag}
			<button type="button" 
					class="btn btn-default"
					data-url="{'cms/tag/topic2tags'|app}"
					target="ajax"					
					data-grp="#page-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要加入内链的{$pageTypeName}!" 
					data-confirm="你真的要将选中的{$pageTypeName}加入内链吗?"
					><i class="fa fa-link"></i> 内链
			</button>
			{/if}
			{if $canEditPage}
			<button type="button" id="btn-move-page" class="btn btn-warning"><i class="fa fa-exchange"></i> 移动</button>
			<button type="button" id="btn-change-flag" class="btn btn-success"><i class="fa fa-list"></i> 属性</button>
			{/if}
			{if $canDelPage}
			<button type="button" 
					class="btn btn-danger"
					data-url="{'cms/page/del'|app}"
					target="ajax"					
					data-grp="#page-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的{$pageTypeName}!" 
					data-confirm="你真的要删除选中的{$pageTypeName}吗?"
					><i class="glyphicon glyphicon-trash"></i> 删除
			</button>
			{/if}
		</div>		
	</div>
</div>
<section id="widget-grid" class="hasr">
    <span class="barhr">
        <i class="fa fa-search"></i>
    </span>
	<div class="rightbar">
        <div class="panel panel-default">
            <div class="panel-heading">
                搜索
            </div>
            <div class="panel-body no-padding">
                <form data-widget="nuiSearchForm" data-for="#page-table" class="smart-form">
                    <fieldset>
						<section>
							<label class="input">
								<i class="icon-append fa fa-calendar"></i>
								<input id="log-from-date" data-widget="nuiDatepicker"
									   data-range-to ="log-to-date" value="{$weekago}"
									   type="text" placeholder="开始时间" name="bd"/>
							</label>
						</section>
						<section>
							<label class="input">
								<i class="icon-append fa fa-calendar"></i>
								<input id="log-to-date" data-widget="nuiDatepicker"
									   data-range-from ="log-from-date" value="{$today}"
									   type="text" placeholder="结束时间" name="sd"/>
							</label>
						</section>
                            <section>
                                <label class="input">
                                    <input type="text" placeholder="ID" name="pid"/>
                                </label>
                            </section>
                            <section>
                                <label class="input">
                                    <input type="text" placeholder="关键词" name="keywords"/>
                                </label>
                            </section>
                            {if $my=='all'}
                                <section>
                                    <label class="input" for="uuname">
                                        <input type="hidden"
                                               data-widget="nuiCombox"
                                               style="width:100%"
                                               placeholder="作者"
                                               data-source="{'system/ajax/autocomplete/user/user_id/nickname/r:cms'|app}" name="uuname" id="uuname"/>
                                    </label>
                                </section>
                            {/if}
							<section>
								<label class="input" for="upname">
									<input type="hidden"
										   data-widget="nuiCombox"
										   style="width:100%"
										   placeholder="编辑"
										   data-source="{'system/ajax/autocomplete/user/user_id/nickname/r:cms'|app}" name="upname" id="upname"/>
								</label>
							</section>
                            <section>
                                {channel_tree name=channel type=$is_topic id=channel value=$channel multi=1 placeholder="请选择栏目"}
                            </section>
                            <section>
                                <label class="select">
                                    <select name="model" id="model">
                                        {html_options options=$models}
                                    </select>
                                    <i></i>
                                </label>
                            </section>
                            {if $disable_approving}
                                <section>
                                    <label class="select">
                                        <select name="status" id="status">
                                            {html_options options=$status selected=$pstatus}
                                        </select>
                                        <i></i>
                                    </label>
                                </section>
                            {/if}
                        {if $widgets}{$widgets|render}{/if}
                        <section>
                            <div class="inline-group">
                                <label class="checkbox">
                                    <input type="checkbox" name="flag_h">
                                    <i></i>头条[h]</label>
                                <label class="checkbox">
                                    <input type="checkbox" name="flag_c">
                                    <i></i>推荐[c]</label>
                                <label class="checkbox">
                                    <input type="checkbox" name="flag_a">
                                    <i></i>特荐[a]</label>
                                <label class="checkbox">
                                    <input type="checkbox" name="flag_b">
                                    <i></i>加粗[b]</label>
                                <label class="checkbox">
                                    <input type="checkbox" name="flag_j">
                                    <i></i>跳转[j]</label>
                            </div
                        </section>
                        <section>
                            <button class="btn btn-sm btn-primary" type="submit">
                                <i class="fa fa-search"></i> <span>搜索</span>
                            </button>
                        </section>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
	<div class="row">
		<article class="col col-sm-12">
			<div class="panel panel-default">
				<div class="table-responsive">
				<table 
					id="page-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'cms/page/data'|app}{$my}/{$type}"
					data-sort="CP.id,d"	
					data-tfoot="true"
					data-tree="true"
					style="min-width: 900px"
				>
					<thead>
						<tr>
							<th width="20" class="hidden-xs hidden-sm"></th>
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="60">ID</th>
							<th>{$pageTypeName}标题</th>
							<th width="100" data-sort="CP.channel,a" class="hidden-xs hidden-sm">栏目</th>
							{if $disable_approving}
							<th width="100" data-sort="CP.status,d" class="hidden-xs hidden-sm">状态</th>
							{/if}
							<th width="200" data-sort="CP.create_time,d" class="hidden-xs hidden-sm">作者/编辑</th>
							<th width="120" data-sort="CP.update_time,d" class="hidden-xs hidden-sm">最后更新</th>
							{if $canEditPage}
							<th width="60" data-sort="display_sort" class="hidden-xs hidden-sm">排序</th>	
							{/if}
							<th width="70"></th>
						</tr>
					</thead>
				</table>
				</div>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#page-table" data-limit="20"></div>
				</div>			
			</div>
		</article>
	</div>
</section>
<div id="catalog-move-page-dialog" class="hidden">
	<div class="row smart-form">
		<section class="col col-xs-12">
			<label class="select">
				<select name="channel" class="channel2">
					{html_options options=$channels}
				</select>
				<i></i>
			</label>
		</section>				
	</div>
	<div class="row smart-form">
		<section class="col col-xs-6">									
			<div class="inline-group">
				<label class="checkbox">
					<input type="checkbox" class="update_url">
					<i></i>更新URL</label>				
			</div>
		</section>
		{if $disable_approving && $canSubmitPage}
		<section class="col col-xs-6">									
			<div class="inline-group">
				<label class="checkbox">
					<input type="checkbox" class="approveit">
					<i></i>移动后送审</label>				
			</div>
		</section>
		{/if}
	</div>
	<section class="text-left text-danger" style="max-width:360px;">
		目标栏目的模型必须和选定文章的模型一致,否则程序会自动忽略不符合的文章.
	</section>
</div>
<div id="catalog-page-flag-dialog" class="hidden">
	<div class="row smart-form">
		<section class="col col-xs-12">									
			<div class="inline-group">
				<label class="checkbox">
					<input type="checkbox" class="flag" value="h">
					<i></i>头条[h]</label>
				<label class="checkbox">
					<input type="checkbox" class="flag" value="c">
					<i></i>推荐[c]</label>
				<label class="checkbox">
					<input type="checkbox" class="flag" value="a">
					<i></i>特荐[a]</label>
				<label class="checkbox">
					<input type="checkbox" class="flag" value="b">
					<i></i>加粗[b]</label>
				<label class="checkbox">
					<input type="checkbox" class="flag" value="j">
					<i></i>跳转[j]</label>
			</div>
		</section>		
	</div>
	<section class="text-left text-danger" style="max-width:360px;">
		注:如果全不选,则会清空文章的所有属性.
	</section>
</div>
<script type="text/javascript">
	$('#page-table').delegate('.ch-item-sort','change',function(){
		var sort = $(this).val();
		if(/^\d?\d?\d$/.test(sort)){
			var id = $(this).parents('tr').attr('rel');
			nUI.ajax("{'cms/page/csort'|app}",{ 
					element:$(this),
					data:{ id:id,sort:sort },
					blockUI:true,
					type:'POST'
			});	
		}
	});
	function get_selected_pages(){
		var ids = [];
		$('#page-table tbody input.grp:checked').each(function(i,e){
			ids.push($(e).val());
		});
		if(ids.length == 0){
			alert('请选择要移动的文章.');			
		}
		return ids;
	}
	$('#btn-move-page').click(function(){
		var ids = get_selected_pages();
		if(ids.length == 0){
			return;
		}
		ids = ids.join(',');
		var dg = new nUI.Dialog('catalog-move-page2', '移动到', {
            model : true,
            icon:'fa fa-exchange',
            theme:'warning',            
            content:function(dg){
            	return $('#catalog-move-page-dialog').html();
            }
        });
    	dg.openLocal(false,[{
    		text:'确定',
    		cls:'btn-primary',
    		click:function(dialog){
    			var ch = dialog.find('.channel2').val();
    			if(!ch){
    				alert('请选择目的栏目.');
    				return false;
    			}
    			var update = dialog.find('.update_url:checked').length>0;
    			var approveit = dialog.find('.approveit:checked').length>0;
    			nUI.ajax("{'cms/page/move'|app}",{ element:$('#btn-move-page'), data:{ ids:ids,ch:ch,upurl:update?'1':'',approve:approveit?'1':'' } });
    			return true;
    		}
    	},{
    		text:'取消'
    	}]);
	});
	$('#btn-change-flag').click(function(){
		var ids = get_selected_pages();
		if(ids.length == 0){
			return;
		}
		ids = ids.join(',');
		var dg = new nUI.Dialog('catalog-page-flag', '修改页面属性', {
            model : true,
            icon:'fa fa-list',
            theme:'success',            
            content:function(dg){
            	return $('#catalog-page-flag-dialog').html();
            }
        });
    	dg.openLocal(false,[{
    		text:'确定',
    		cls:'btn-primary',
    		click:function(dialog){
    			var flags =[]; 
    			dialog.find('.flag:checked').each(function(i,e){
    				flags.push($(e).val());	
    			});    			
    			nUI.ajax("{'cms/page/flags'|app}",{ element:$('#btn-change-flag'), data:{ ids:ids,flags:flags.join(',') } });
    			return true;
    		}
    	},{
    		text:'取消'
    	}]);
	});
	
</script>