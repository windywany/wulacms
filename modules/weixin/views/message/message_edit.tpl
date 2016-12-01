<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">			
			<i class="fa fa-fw fa-twitter"></i>
			 {if $msgType == 1}
			 	订阅消息回复
			 {/if}
			 {if $msgType == 2}
			 	自动消息回复
			 {/if}
			 {if $msgType == 3}
			 	关键字消息回复
			 {/if}
		</h1>
	</div>
	 {if $msgType == 3}
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
			<div class="pull-right margin-top-5 margin-bottom-5">
				<a  class="btn btn-default" href="#{'weixin/index'|app:0}" id="rtnbtn">
					<i class="glyphicon glyphicon-circle-arrow-left"></i> 返回
				</a>			
			</div>
		</div>
	{/if}
	
</div>
<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body no-padding">
					<ul class="nav nav-tabs">
						{foreach from=$msgTypeList item=val key=k}
						<li {if $k == $data.msg_type}class="active"{/if} >
							<a data-toggle="tab" role="tab" href="#sub_{$k}"><span>{$val}</span></a>
						</li>
						{/foreach}
					</ul>
					<div class="tab-content">
						{if 'text' == $data.msg_type}
							<div id="sub_text" class="tab-pane active fade in">
						{else}
							<div id="sub_text" class="tab-pane fade in">
						{/if}
								<form data-widget="nuiValidate" name="{$text.form}" id="{$text.form}" action="{'weixin/message/msgsave'|app}" method="post" class="smart-form" target="ajax">
							  		<input type="hidden" name="msg_id" value="{$data.id}"/>
							  		<input type="hidden" name="msg_table" value="{$msgType}"/>
	  						  		<input type="hidden" name="msg_type" value="text"/>
							  		<fieldset>
							  			{$text.widgets|render}
							  		</fieldset>
							  		<footer>
										<button class="btn btn-primary" type="submit">
											保存
										</button>
										<button class="btn btn-default" type="reset">
											重置
										</button>
									</footer>
							  	</form>		
						</div>
						{if 'image' == $data.msg_type}
							<div id="sub_image" class="tab-pane active fade in">
						{else}
							<div id="sub_image" class="tab-pane fade in">
						{/if}
								<form data-widget="nuiValidate" name="{$image.form}" id="{$image.form}" action="{'weixin/message/msgsave'|app}" method="post" class="smart-form" target="ajax">
							  		<input type="hidden" name="msg_id" value="{$data.id}"/>
							  		<input type="hidden" name="msg_table" value="{$msgType}"/>
	  						  		<input type="hidden" name="msg_type" value="image"/>
							  		<fieldset>
							  			{$image.widgets|render}
							  		</fieldset>
							  		<footer>
										<button class="btn btn-primary" type="submit">
											保存
										</button>
										<button class="btn btn-default" type="reset">
											重置
										</button>
									</footer>				  		
							  	</form>
						</div>
						{if 'voice' == $data.msg_type}
							<div id="sub_voice" class="tab-pane active fade in">
						{else}
							<div id="sub_voice" class="tab-pane fade in">
						{/if}
								<form data-widget="nuiValidate" name="{$voice.form}" id="{$voice.form}" action="{'weixin/message/msgsave'|app}" method="post" class="smart-form" target="ajax">
							  		<input type="hidden" name="msg_id" value="{$data.id}"/>
							  		<input type="hidden" name="msg_table" value="{$msgType}"/>
	  						  		<input type="hidden" name="msg_type" value="voice"/>
							  		<fieldset>
							  			{$voice.widgets|render}
							  		</fieldset>
							  		<footer>
										<button class="btn btn-primary" type="submit">
											保存
										</button>
										<button class="btn btn-default" type="reset">
											重置
										</button>
									</footer>				  		
							  	</form>
						</div>
						{if 'video' == $data.msg_type}
							<div id="sub_video" class="tab-pane active fade in">
						{else}
							<div id="sub_video" class="tab-pane fade in">
						{/if}
								<form data-widget="nuiValidate" name="{$video.form}" id="{$video.form}" action="{'weixin/message/msgsave'|app}" method="post" class="smart-form" target="ajax">
							  		<input type="hidden" name="msg_id" value="{$data.id}"/>
							  		<input type="hidden" name="msg_table" value="{$msgType}"/>
	  						  		<input type="hidden" name="msg_type" value="video" />
							  		<fieldset>
							  			{$video.widgets|render}
							  		</fieldset>
							  		<footer>
										<button class="btn btn-primary" type="submit">
											保存
										</button>
										<button class="btn btn-default" type="reset">
											重置
										</button>
									</footer>				  		
							  	</form>
						</div>
						{if in_array('music',$keyList)}
							{if 'music' == $data.msg_type}
								<div id="sub_music" class="tab-pane active fade in">
							{else}
								<div id="sub_music" class="tab-pane fade in">
							{/if}
									<form data-widget="nuiValidate" name="{$music.form}" id="{$music.form}" action="{'weixin/message/msgsave'|app}" method="post" class="smart-form" target="ajax">
								  		<input type="hidden" name="msg_id" value="{$data.id}"/>
								  		<input type="hidden" name="msg_table" value="{$msgType}"/>
		  						  		<input type="hidden" name="msg_type" value="music" />
								  		<fieldset>
								  			{$music.widgets|render}
								  		</fieldset>
								  		<footer>
											<button class="btn btn-primary" type="submit">
												保存
											</button>
											<button class="btn btn-default" type="reset">
												重置
											</button>
										</footer>
								  	</form>
							</div>
						{/if}
						
						{if in_array('news',$keyList)}
						
							{if 'news' == $data.msg_type}
								<div id="sub_news" class="tab-pane active fade in">
							{else}
								<div id="sub_news" class="tab-pane fade in">
							{/if}
								<form data-widget="nuiValidate" name="{$news.form}" id="{$news.form}" action="{'weixin/message/msgsave'|app}" method="post" class="smart-form" target="ajax">
							  		<input type="hidden" name="msg_id" value="{$data.id}"/>
							  		<input type="hidden" name="msg_table" value="{$msgType}"/>
	  						  		<input type="hidden" name="msg_type" value="news" />
								  		<fieldset>
								  			<input type="hidden" id="id" name="id" value="{$news.data.id}" />
								  			<div class="row">
									  			<section class="col col-3">
									  				<label class="label" for="title">cms 区块</label>
									  				<label class="input">
									  					<input type="hidden" 
															data-widget="nuiCombox" 
															style="width:100%"
															placeholder="区块名称"
															data-source="{'system/ajax/autocomplete/cms_block/id/name/r:cms'|app}" name="block_id"  value="{$news.data.name}"/>
								  					</label>
								  					<div class="note">请选择 cms 区块，最多条数位10</div>
							  					</section>
	 										</div>
								  		</fieldset>
								  		<footer>
											<button class="btn btn-primary" type="submit">
												保存
											</button>
											<button class="btn btn-default" type="reset">
												重置
											</button>
										</footer>				  		
								  	</form>
							</div>
						{/if}
						
						
					</div>
				</div>
			</div>
		</article>
	</div>
</section>
<script type="text/javascript">    
$(function(){
/*
  $('select').select2().on("change", function(e) {
	  consle.log('asdf');
  });
	*/
});
</script>
