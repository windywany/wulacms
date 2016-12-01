<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-sitemap"></i> {$channelType}
			<span>&gt; 编辑{$channelType}</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default" href="#{'weixin/menu'|app:0}{if $type}{$type}{/if}" id="rtnbtn">
				<i class="glyphicon glyphicon-circle-arrow-left"></i> 返回
			</a>			
		</div>
	</div>
</div>

<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="jarviswidget"
                id="wid-id-1"     
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
                     <h2> {$channelType}编辑器 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="ChannelForm"                          		
                          		data-widget="nuiValidate" action="{'weixin/menu/save'|app}" 
                          		method="post" id="channel-form" class="smart-form" target="ajax"
                          		>
                          	<input type="hidden" id="channel_id" name="id" value="{$id}"/>
							<fieldset>								
								<div class="row">
									{if !$type}
									<section class="col col-4">
										<label class="label">上级网站栏目</label>
										{weixin_tree name=upid id=upid value=$upid cid=$id type=$type placeholder="顶级栏目"}
									</section>
									{else}
									<input type="hidden" name="upid" value="0"/>
									{/if}
									<section class="col col-8">
										<label class="label">{$channelType}名称</label>
										<label class="input">
										<input type="text" name="name" 
											id="name" value="{$name}" />
										</label>
									</section>
								</div>
											
								<div class="row">
									<section class="col col-4">
										<label class="label">菜单类型</label>
										<label class="select">
											<select name="menu_type" id="menu_type">
												{html_options options=$typeList selected=$menu_type}
											</select>
											<i></i>
										</label>
									</section>
									
									<section class="col col-4">
										<label class="label">VALUE</label>
										<label class="input">
										<input type="text" name="key" 
											id="key" value="{$key}" />
										</label>
										<div class="note">菜单对应值。</div>
									</section>
									<section class="col col-4">
										<label class="label">显示排序</label>
										<label class="input">
										<input type="text" name="sort" 
											id="sort" value="{$sort}" />
										</label>
										<div class="note">取值范围0-999,值越小越靠前。</div>
									</section>
								</div>
							</fieldset>

							<footer>
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								<a  class="btn btn-default" href="#{'weixin/menu'|app:0}{if $type}{$type}{/if}">
									返回
								</a>
							</footer>
						</form>

                     </div>
                </div>
           </div>
		</article>
	</div>
</section>
<script type="text/javascript">
	nUI.validateRules['ChannelForm'] = {$rules};
	nUI.ajaxCallbacks['InvalidListPagePattern'] = function(data){
		alert(data.id);
		$('#channel_id').val(data.id);
	};
</script>