<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-comments-o"></i> 编辑评论			
		</h1>
	</div>
</div>
<section id="widget-grid">
	<form name="CommentEditForm"
		  data-widget="nuiValidate"                          		
          action="{'comment/save'|app}" 
          method="post" id="CommentEditForm" target="ajax" >
		<div class="row">
			<article class="col-sm-8" id="comment-edit-main-wrap">
				<div class="panel panel-default">
					<div class="panel-body smart-form">
						{$widgets|render}
					</div>
				</div>
				<div class="panel panel-default quicktags">
					<textarea rows="10" name="comment_content" id="comment_content">{$comment.content|escape}</textarea>
				</div>
				{if $canReplyComment}
				<div style="margin-top:10px;">
					<h6>回复评论</h6><div class="quicktags" id="reply-cmt"></div>
				</div>
				{/if}
			</article>
			<article class="col-sm-4">
				<div class="panel panel-default">
					<div class="panel-body no-padding smart-form">
						<fieldset>
							<legend>审核</legend>
							<section>							
							<label class="radio txt-color-green">
								<input type="radio" {if $comment.status =='1'}checked="checked"{/if} name="status" value="1" {if $cannotApproveComment}disabled="disabled"{/if}>
								<i></i>已审核</label>
							<label class="radio txt-color-orange">
								<input type="radio" name="status" {if $comment.status =='0'}checked="checked"{/if} value="0" {if $cannotApproveComment}disabled="disabled"{/if}>
								<i></i>待审</label>
							<label class="radio txt-color-red">
								<input type="radio" name="status" {if $comment.status =='2'}checked="checked"{/if} value="2" {if $cannotApproveComment}disabled="disabled"{/if}>
								<i></i>垃圾评论</label>
							</section>
							<section>
								IP地址:<strong><a href="http://whois.arin.net/rest/ip/{$comment.autor_ip}" target="_blank">{$comment.author_ip}</a></strong>
							</section>											
						</fieldset>
						<footer>						
							<a href="#{'comment'|app:0}?status={$comment.status}" class="btn btn-default pull-left">返回</a>
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
	nUI.validateRules['CommentEditForm'] = {$rules};
	nUI.ajaxCallbacks.commentSaved = function(){
		$('#replycontent').val('');
	};
</script>