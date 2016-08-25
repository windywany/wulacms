<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">			
			<i class="fa fa-fw fa-comments-o"></i> 评论
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<div class="btn-group">				
				{if $canDelComment}
				<a href="{'comment/del'|app}" target="ajax"  
				    data-grp="#comment-table tbody input.grp:checked" 
					data-arg="ids" 
					data-warn="请选择要删除的评论!" 
					data-confirm="你真的要删除选中的评论吗?"
				    class="btn btn-danger">
					<i class="fa fa-trash-o"></i></a>									
				{else}				
				<button data-toggle="dropdown" class="btn btn-primary dropdown-toggle">
					<i class="fa fa-list"></i>
				</button>				
				{/if}
				{if $canApproveComment}
				<button data-toggle="dropdown" class="btn btn-default dropdown-toggle">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu pull-right">
					
					<li><a href="{'comment/status'|app}0"
						target="ajax"  
					    data-grp="#comment-table tbody input.grp:checked" 
						data-arg="ids" 
						data-warn="请选择要驳回的评论!" 
						data-confirm="你真的要驳回选中的评论吗?"
						>
						<i class="fa fa-thumbs-o-down txt-color-blue"></i> 驳回</a></li>						
						<li><a href="{'comment/status'|app}1"
						target="ajax"  
					    data-grp="#comment-table tbody input.grp:checked" 
						data-arg="ids" 
						data-warn="请选择要批准的评论!" 
						data-confirm="你真的要批准选中的评论吗?"
						>
						<i class="fa fa-thumbs-o-up txt-color-green"></i> 批准</a></li>
					<li><a href="{'comment/status'|app}2"
						target="ajax"  
					    data-grp="#comment-table tbody input.grp:checked" 
						data-arg="ids" 
						data-warn="请选择要标记为垃圾的评论!" 
						data-confirm="你真的要标记选中的评论为垃圾评论吗?"
						>
						<i class="fa fa-bug txt-color-orange"></i> 标记为垃圾评论</a></li>
				</ul>	
				{/if}
			</div>
									
		</div>
	</div>
</div>
<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body no-padding">
					<ul class="nav nav-tabs in" id="comment-status-tab">
						<li {if $status==''}class="active"{/if}>
							<a href="#" rel="" id="allcs"><span>全部</span></a>
						</li>
						<li {if $status==='0'}class="active"{/if}>
							<a href="#" class="txt-color-blue" rel="0"><span>{$status_text[0]}</span></a>
						</li>
						<li {if $status==='1'}class="active"{/if}>
							<a href="#" class="txt-color-green" rel="1"><span>{$status_text[1]}</span></a>
						</li>
						<li {if $status==='2'}class="active"{/if}>
							<a href="#" class="txt-color-orange" rel="2"><span>{$status_text[2]}</span></a>
						</li>
					</ul>			  
				  	<form data-widget="nuiSearchForm" id="comment-search-form" data-for="#comment-table" class="smart-form">
				  		<input type="hidden" name="status" id="status" value="{$status}"/>				  		
				  		<fieldset>
				  			<div class="row">
				  				<section class="col col-3">
									<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<input type="text" placeholder="关键词" name="keywords" id="keywords"/>
									</label>
								</section>
								
								<section class="col col-3">
									<label class="input">
										<i class="icon-prepend fa fa-user"></i>
										<input type="text" placeholder="用户" name="author" id="author"/>
									</label>
								</section>
								
								<section class="col col-2">
									<label class="input">
										<i class="icon-prepend fa fa-phone"></i>
										<input type="text" placeholder="联系方式" name="contact" id="contact"/>
									</label>
								</section>									
								<section class="col col-2">
									<label class="input">
										<i class="icon-prepend fa fa-filter"></i>
										<input type="text" placeholder="页面" name="page_id" id="page_id"/>
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
					id="comment-table"
					data-widget="nuiTable"		
					data-auto="true"
					data-source="{'comment/data'|app}"
					data-sort="C.id,d"					
					>
					<thead>
						<tr>							
							<th width="30"><input type="checkbox" class="grp"/></th>
							<th width="220" data-sort="C.create_uid,d">用户</th>					
							<th>内容</th>							
							<th data-sort="page_id,d" width="150">评论页面</th>	
							<th width="70"></th>							
						</tr>
					</thead>					
				</table>
				<div class="panel-footer">
					<div data-widget="nuiPager" data-for="#comment-table" data-limit="10"></div>
				</div>			
			</div>
		</article>
	</div>
</section>
<script type="text/javascript">

$('#comment-status-tab a').click(function(){ 
	$('#comment-status-tab li').removeClass('active');
	$(this).parents('li').addClass('active');
	var rel = $(this).attr('rel');
	$('#status').val(rel);
	$('#comment-search-form').submit();
	return false;
});

function comment_setpage_id(id){		
	$('#keywords').val('');
	$('#author').val('');
	$('#contact').val('');
	$('#page_id').val(id);
	$('#allcs').click();
}

function comment_select_ip(ip){
	$('#keywords').val('');
	$('#author').val(ip);
	$('#contact').val('');
	$('#page_id').val('');
	$('#allcs').click();
}

function reply_comment(id){
	var cid = '#comment-id-'+id;
	var rid = '#comment-reply-form-'+id;
	if($(rid).length == 0){
		var tr = '<tr id="comment-reply-form-'+id+'" class="reply-form"><td colspan="5"><h6>回复评论</h6><div class="quicktags"></div>\
		<div style="margin-top:5px;"><a href="javascript:;" onclick="cancel_reply_comment()" class="btn btn-default pull-left">取消</a>\
		<a href="javascript:;" onclick="do_reply_comment('+id+',this)" class="btn btn-success pull-right">回复评论</a>\
		</div></td></tr>';
		$(tr).insertAfter($(cid));
		$('.quicktags').quicktags({
			'name':'replycontent',
			'height':'100'
		});
	}
}

function cancel_reply_comment(){
	$('tr.reply-form').remove();
}
function do_reply_comment(id,btn){
	var content = $('#replycontent').val();
	if(content.trim()){
		nUI.ajax("{'comment/reply'|app}",{
			blockUI:true,
			element:$(btn),
			type:'POST',			
			data:{
				id:id,
				content:content
			}
		});
	}else{
		alert('请填点内容嘛.');
	}
}
</script>
