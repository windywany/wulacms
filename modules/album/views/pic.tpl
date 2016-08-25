<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-picture-o"></i> 
			<a href="#{'album'|app:0}">相册列表</a>
			<span>&gt; {$album_name}</span>
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			{if $canAddPage}
			<a class="btn btn-primary" target="dialog" dialog-title="下载网络图片" dialog-model="true" dialog-width="700" dialog-id="download-album-pic" href="{'album/download'|app}{$album_id}">				
					<i class="glyphicon glyphicon-cloud-download"></i> 网络下载
			</a>
			<a class="btn btn-success" href="#{'album/upload'|app:0}{$album_id}">				
					<i class="glyphicon glyphicon-cloud-upload"></i> 上传
			</a>
			{/if}
			{if $canDelPage}
			<button type="button" 
					class="btn btn-danger"
					data-url="{'album/del'|app}"
					target="ajax"
					data-grp="#page-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的相片!" 
					data-confirm="你真的要删除选中的相片吗?"
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
				  				<section class="col col-md-4">
									<label class="input">										
										<input type="text" placeholder="关键词" name="keywords"/>
									</label>
								</section>		
								<section class="col col-md-6">
									<div class="inline-group"><label class="checkbox"><input type="checkbox" name="flag_hot" id="flag_hot"><i></i>推荐</label></div>
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
					data-source="{'album/pic_data'|app}{$album_id}"
					data-sort="is_hot,d"	
					data-tfoot="true"
					data-tree="true">
					<thead>
						<tr>							
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="150"></th>							
							<th width="220" data-sort="title,a">名称</th>
							<th class="hidden-xs hidden-sm">描述</th>
							<th width="150" data-sort="PIC.create_uid,a">上传者</th>
							<th width="150" data-sort="PIC.create_time,d">上传时间</th>
							<th width="80" data-sort="is_hot,a">推荐</th>
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
<script type="text/javascript">
nUI.ajaxCallbacks['AlbumPicSaved'] = function(){
	nUI.closeDialog('edit-album-pic');
	var obj = $('#page-table').data('reloadObj');
    if(obj){
        obj.reload(null,true);
    }    
}
nUI.ajaxCallbacks['AlbumPicDownloaded'] = function(){
	nUI.closeDialog('download-album-pic');
	var obj = $('#page-table').data('reloadObj');
    if(obj){
        obj.reload(null,true);
    }    
}
function album_is_hot_change(pic){
	var sort = $(pic).prop('checked')?1:0;
	var id = $(pic).parents('tr').attr('rel');
	nUI.ajax("{'album/sethot'|app}",{ 
		element:$(pic),
		data:{ id:id,hot:sort },
		blockUI:true,
		type:'POST'
	});	
}
</script>