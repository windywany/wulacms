<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-sitemap"></i> {$channelType}
			<span>&gt; 编辑{$channelType}</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default" href="#{'cms/channel'|app:0}{if $type}{$type}{/if}" id="rtnbtn">
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
                          		data-widget="nuiValidate" action="{'cms/channel/batsave'|app}" 
                          		method="post" id="channel-form" class="smart-form" target="ajax"
                          		>
                          	<input type="hidden" id="channel_id" name="id" value="{$id}"/>
                          	<input type="hidden" name="is_topic_channel" value="{$type}"/>
                          	<input type="hidden" name="list_page" value="{$list_page}"/>
                          	<input type="hidden" name="index_page" value="{$index_page}"/>
                          	<input type="hidden" name="oupid" value="{$oupid}">
                          	<input type="hidden" name="refid" value="refid">
                          	<input type="hidden" name="basedir" value="basedir">
                          	<input type="hidden" name="sort" value="0">
							<fieldset>								
								<div class="row">
									{if !$type}
									<section class="col col-4">
										<label class="label">上级网站栏目</label>
										<label class="select">
											<select name="upid" id="upid">
												{html_options options=$channels selected=$upid}
											</select>
											<i></i>
										</label>
									</section>
									{else}
									<input type="hidden" name="upid" value="0"/>
									{/if}	
								</div>
											
								<div class="row">
									<section class="col col-4">
										<label class="label">内容模型</label>
										<label class="select">
											<select name="default_model" id="default_model">
												{html_options options=$models selected=$default_model}
											</select><i></i>
										</label>
									</section>
									{if $enable_group_bind}
									<section class="col col-4">
										<label class="label">绑定到用户组</label>
										<label class="select">
											<select name="gid" id="gid">
												{html_options options=$groups selected=$gid}
											</select><i></i>
										</label>
									</section>
									{else}
									<input type="hidden" name="gid" id="gid" value="{$gid}"/>
									{/if}						
								</div>
								<div class="row">
									<section class="col col-8">
										<label class="label">{$channelType}名称</label>
										<label class="textarea">
										<textarea name="name" id="name" rows="6" />{$name}
										</textarea>
										</label>
										<div class="note">例如：国内新闻|china 每行一个，竖线以及后面的英文名可留空，默认会自动生成栏目的拼音。</div>
									</section>
								</div>
								{if !$type}								
								<section>									
									<div class="inline-group">										
										<label class="checkbox">
											<input type="checkbox" name="isfinal" {if $isfinal}checked="checked"{/if}/>
											<i></i>最终列表栏目（允许在本栏目发布文档，并生成文档列表）
										</label>																	
										<label class="checkbox">
											<input type="checkbox" name="hidden" {if $hidden}checked="checked"{/if}/>
											<i></i>创建时不可见(包括子栏目).
										</label>
									</div>
								</section>
								{else}
								<input type="hidden" name="isfinal" value="on"/>
								<input type="hidden" name="hidden" value=""/>
								{/if}
								<section class="timeline-seperator txt-primary"> 
									<span>模板与默认页设置</span>
								</section>								
								<div class="row">
									<section class="col col-4">
										<label class="label">封面页模板</label>
										<label class="input" for="index_page_tpl">										
											<input type="hidden" data-widget="nuiCombox" style="width:100%"	data-source="{'system/ajax/tpl'|app}"
												   name="index_page_tpl" id="index_page_tpl" value="{$index_page_tpl}"/>										
										</label>
									</section>
									<section class="col col-6">									
										<label class="label">
											默认页的名称											
										</label>
										<label class="input">
										<i class="icon-append fa fa-question-circle"></i>
										<input type="text" name="page_name" 
											id="page_name" value="{$page_name}" />
											<b class="tooltip tooltip-top-left">
											{literal}											
											{path} 全路径(默认)<br/>
											{rpath} 退一格路径（用于二级域名时）<br/>
											{/literal}
										</b>
										</label>
									</section>
									<section class="col col-2">
										<label class="label">缓存时间</label>
										<label class="input">
										<input type="text" name="page_cache" 
											id="page_cache" value="{$page_cache}" />
											<b class="tooltip tooltip-top-left">												
												-1表示不缓存<br>
												0或不填写表示使用上级设置<br>
												其它数值表示此页面的缓存时间					
											</b>
										</label>
									</section>
								</div>
								<div class="row">
									<section class="col col-4">
										<label class="label">文章页模板</label>
										<label class="input" for="default_template">
											<input type="hidden" data-widget="nuiCombox" style="width:100%"	data-source="{'system/ajax/tpl'|app}"
												   name="default_template" id="default_template" value="{$default_template}"/>										
										</label>
									</section>
									<section class="col col-6">									
										<label class="label">文章页命名规则</label>
										<label class="input">
										<i class="icon-append fa fa-question-circle"></i>
										<input type="text" name="default_url_pattern" 
											id="default_url_pattern" value="{$default_url_pattern}" />
											<b class="tooltip tooltip-top-left">
												{literal}
												{Y}、{M}、{D} 年月日<br/>
												{timestamp} INT类型的UNIX时间戳<br/>
												{aid} 文章ID<br/>
												{pinyin} 拼音+文章ID<br/>
												{py} 拼音部首+文章ID<br/>
												{typedir} 栏目目录<br/>
												{path} 全路径<br/>
												{rpath} 退一格路径（用于二级域名时）<br/>
												{cc} 日期+ID混编后用转换为适合的字母<br/>
												{tid} 栏目编号<br/>
												{cid} 栏目识别ID 
												{/literal}
											</b>
										</label>										
									</section>
									<section class="col col-2">
										<label class="label">缓存时间</label>
										<label class="input">
										<input type="text" name="default_cache" 
											id="default_cache" value="{$default_cache}" />
											<b class="tooltip tooltip-top-left">												
												-1表示不缓存<br>
												0或不填写表示使用上级设置<br>
												其它数值表示此页面的缓存时间					
											</b>
										</label>
									</section>
								</div>
								<div class="row">
									<section class="col col-4">
										<label class="label">列表页模板</label>
										<label class="input" for="list_page_tpl">
											<input type="hidden" data-widget="nuiCombox" style="width:100%"	data-source="{'system/ajax/tpl'|app}"
												   name="list_page_tpl" id="list_page_tpl" value="{$list_page_tpl}"/>
										</label>
									</section>
									<section class="col col-6">									
										<label class="label">列表页命名规则</label>
										<label class="input">
										<i class="icon-append fa fa-question-circle"></i>
										<input type="text" name="list_page_name" 
											id="list_page_name" value="{$list_page_name}" />
										<b class="tooltip tooltip-top-left">
											{literal}
											{pinyin} 拼音+文章ID<br/>
											{py} 拼音部首+文章ID<br/>
											{typedir} 栏目目录<br/>
											{path} 全路径<br/>
											{rpath} 退一格路径（用于二级域名时）<br/>
											{cc} 日期+ID混编后用转换为适合的字母<br/>
											{page} 列表的页码<br/>
											{tid} 栏目编号<br/>
											{cid} 栏目识别ID 
											{/literal}
										</b>
										</label>
									</section>
									<section class="col col-2">
										<label class="label">缓存时间</label>
										<label class="input">
										<input type="text" name="list_cache" 
											id="list_cache" value="{$list_cache}" />
											<b class="tooltip tooltip-top-left">												
												-1表示不缓存<br>
												0或不填写表示使用上级设置<br>
												其它数值表示此页面的缓存时间					
											</b>
										</label>
									</section>
								</div>
								
								<section>									
									<div class="inline-group">										
										<label class="checkbox">
											<input type="checkbox" name="default_page" {if $default_page}checked="checked"{/if}/>
											<i></i> 默认链接到列表第一页(不选使用封面页)
										</label>
									</div>
								</section>
								<input type="hidden" name="title" value=""/>
								<input type="hidden" name="keywords" value=""/>
								<input type="hidden" name="description" value=""/>
																							
							</fieldset>

							<footer>
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								<a  class="btn btn-default" href="#{'cms/channel'|app:0}{if $type}{$type}{/if}">
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