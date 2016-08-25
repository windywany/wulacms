<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-comments-o"></i> 编辑留言			
		</h1>
	</div>
</div>
<section id="widget-grid">
	<form name="CommentMsgEditForm"
		  data-widget="nuiValidate"                          		
          action="{'comment/msg/save'|app}" 
          method="post" id="CommentMsgEditForm" target="ajax" >
		<div class="row">
			{foreach $msgs as $msg}
			<article class="col-sm-8" style="margin-bottom:5px;">
				<div class="panel-group smart-accordion-default" id="comment-accordion">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a href="#comment-collapseOne" data-parent="#cmt-{$msg.id}" data-toggle="collapse"> 
								<i class="fa fa-lg fa-angle-down pull-right"></i> 
								<i class="fa fa-lg fa-angle-up pull-right"></i> {$msg.author} 于 {$msg.create_time|date_format:'Y-m-d H:i'} 留言 </a></h4>
						</div>
						<div id="comment-collapseOne" class="panel-collapse">
							<div class="panel-body">
								{if $msg.subject}<span class="label bg-color-blueLight pull-left">{$msg.subject}</span>{/if}
								{$msg.content|nl2br}
								
								{if $msg.replies}
									{foreach $msg.replies as $rp}
									<div style="padding-left:15px;margin-top:5px;">
										<strong>{$rp.author}</strong> 于	{$rp.update_time|date_format:'Y-m-d H:i'}回复<br/>
										{if $rp.subject}<span class="label bg-color-blue pull-left">{$rp.subject}</span>{/if}
										{$rp.content|nl2br}				
									</div>
									{/foreach}
								{/if}
							</div>
						</div>
					</div>
				</div>
			</article>	
			{/foreach}
			<article class="col-sm-8" id="comment-edit-main-wrap">				
				<div class="no-padding smart-form">
					<section><label class="input"><input type="text" placeholder="留言主题" name="subject" id="subject" value="{$comment.subject|escape}"/></label></section>
				</div>
				<div class="panel panel-default quicktags">
					<textarea rows="10" name="comment_content" id="comment_content">{$comment.content|escape}</textarea>
				</div>
				{if $canReplyComment}
				<div style="margin:10px 0;">
					<h6>回复留言</h6>
					<div class="no-padding smart-form">
						<section><label class="input"><input type="text" placeholder="备注" name="rsubject" id="rsubject"/></label></section>
					</div>
					<div class="quicktags" id="reply-cmt"></div>
				</div>
				{/if}
				<div class="panel panel-default">
					<div class="panel-body smart-form">
						{$widgets|render}
					</div>
				</div>
			</article>
			<article class="col-sm-4">
				<div class="panel panel-default">
					<div class="panel-body no-padding smart-form">
						<fieldset>
							<legend>处理状态</legend>
							<section>							
							<label class="radio txt-color-green">
								<input type="radio" {if $comment.status =='1'}checked="checked"{/if} name="status" value="1" {if $cannotApproveComment}disabled="disabled"{/if}>
								<i></i>已处理</label>
							<label class="radio txt-color-orange">
								<input type="radio" name="status" {if $comment.status =='0'}checked="checked"{/if} value="0" {if $cannotApproveComment}disabled="disabled"{/if}>
								<i></i>待处理</label>
							<label class="radio txt-color-red">
								<input type="radio" name="status" {if $comment.status =='2'}checked="checked"{/if} value="2" {if $cannotApproveComment}disabled="disabled"{/if}>
								<i></i>垃圾留言</label>
							<label class="radio txt-color-teal">
								<input type="radio" name="status" {if $comment.status =='3'}checked="checked"{/if} value="3" {if $cannotApproveComment}disabled="disabled"{/if}>
								<i></i>处理中</label>
							</section>
							<section>
								IP地址:<strong><a href="http://whois.arin.net/rest/ip/{$comment.autor_ip}" target="_blank">{$comment.author_ip}</a></strong>
							</section>											
						</fieldset>
						<footer>						
							<a href="#{'comment/msg'|app:0}?status={$comment.status}" class="btn btn-default pull-left">返回</a>
							<button type="submit" class="btn btn-success">更新</button>																
						</footer>
					</div>
				</div>
			</article>
		</div>
	</form>
</section>
<script type="text/javascript">
	$('.quicktags').quicktags({
		name:'comment_content',
		height:'100'
	});	
	$('#reply-cmt').quicktags({
		'name':'replycontent',
		'height':'100'
	});	
	nUI.validateRules['CommentMsgEditForm'] = {$rules};
	nUI.ajaxCallbacks.commentSaved = function(){
		$('#replycontent').val('');
	};
</script>