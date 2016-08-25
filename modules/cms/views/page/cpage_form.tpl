<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-copy"></i> 自定义页面
			<span>&gt; 编辑页面</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default btn-labeled" href="#{'cms/cpage'|app:0}">
				<span class="btn-label">
					<i class="glyphicon glyphicon-circle-arrow-left"></i>
				</span> 返回
			</a>			
		</div>
	</div>
</div>

<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="jarviswidget"
                id="wid-cpage-form"     
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
                     <h2> 自定义页面编辑器 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="CPageForm"                          		
                          		data-widget="nuiValidate" action="{'cms/cpage/save'|app}" 
                          		method="post" id="page-form" class="smart-form" target="ajax"
                          		>
                          	<input type="hidden" id="id" name="id" value="{$id}"/>
                          	<input type="hidden" id="create_time" name="create_time" value="{$create_time}"/>
                          	<input type="hidden" name="title_color" id="title_color" value="{$title_color}"/>
							<fieldset>												
								<div class="row">									
									<section class="col col-8">
										<label class="label">页面名称</label>
										<label class="input">
										<i class="icon-append fa fa-square" 
											data-plugin="colorpicker"
											data-widget="nuiKindEditor"
											data-for="#title_color"></i>
										<input type="text" name="title2" id="title2" value="{$title2|escape}"/>
										</label>
									</section>									
									<section class="col col-4">
										<label class="label">标签</label>
										<label class="input">
										<input type="text" name="tag" id="tag" value="{$tag}"/>
										</label>
									</section>									
								</div>	
								<div class="row">
									<section class="col col-2">
										<label class="label">&nbsp;</label>
										<div class="inline-group">
											<label class="checkbox">
												<input type="checkbox" {if $is_tpl_page}checked="checked"{/if}  name="is_tpl_page"/>
												<i></i>模板页</label>
										</div>
									</section>
									<section class="col col-6">
										<label class="label">页面URL(必填)</label>
										<label class="input">
											<input type="text" id="url" name="url" value="{$url}"/>
											<b class="tooltip tooltip-top-left">
												{literal}
												1. html 页面可以使用.shtml,.html,.html做为扩展名<br/>
												2. xml 页面请使用.xml做为扩展名<br/>
												3. js 脚本请使用.jsp做为扩展名<br/>
												4. json 数据请使用.json做为扩展名<br/>
												5. 如果此页面为模板页，URL中可以包含命名的正则表达式										
												{/literal}												
											</b>
										</label>
									</section>									
									<section class="col col-4">
										<label class="label">页面模板(必填)</label>
										<label class="input" for="template_file">
											<input type="hidden" data-widget="nuiCombox" style="width:100%"	data-source="{'system/ajax/tpl'|app}"
												   name="template_file" id="template_file" value="{$template_file}"/>											
										</label>
									</section>
								</div>	
								<div class="row">
									<section class="col col-2">
										<label class="label">缓存时间</label>
										<label class="input">
											<input type="text" name="expire" id="expire" value="{$expire}"/>
											<b class="tooltip tooltip-top-left">
												{literal}
												-1表示不缓存<br/>
												0或不填写表示使用系统设置的缓存时间<br/>
												其它数值表示此页面的缓存时间										
												{/literal}												
											</b>
										</label>
									</section>
									{if $url_handlers}
									<section class="col col-4">
										<label class="label">自定义处理器</label>
										<label class="select">
										<select name="url_handler" id="url_handler">
										{html_options options=$url_handlers selected=$url_handler}
										</select><i></i>
										</label>
									</section>
									{/if}
								</div>														
								<div class="row">
									<section class="col col-4">
										<label class="label">SEO标题</label>
										<label class="input">
										<input type="text" name="title" 
											id="title" value="{$title|escape}"/>
										</label>									
									</section>
									<section class="col col-8">
										<label class="label">SEO关键词(以','分开.)</label>
										<label class="input">
										<input type="text" name="keywords" 
											id="keywords" value="{$keywords|escape}"/>
										</label>										
									</section>
								</div>
								<section>
									<label class="label">SEO描述</label>
									<label class="textarea">
									<textarea class="custom-scroll" name="description" rows="3" id="description">{$description|escape}</textarea>
									</label>
								</section>	
								
							</fieldset>
							
							<footer>								
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								{if !$id}
								<a style="display:none" class="btn btn-success" href="{'cms/cpage/add'|app}" target="tag" data-tag="#content" id="btn-c-add">
									再建一篇
								</a>
								{/if}
								<a class="btn btn-default" href="#{'cms/cpage'|app:0}">
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
	nUI.validateRules['CPageForm'] = {$rules};
	nUI.ajaxCallbacks.pageSaved = function(args){
		var page = args.page;
		if(page){
			if(page.is_new){
				$('#id').val(page.id);
				nUI.validateRules['CPageForm'].rules.url.remote = nUI.validateRules['CPageForm'].rules.url.remote+page.id;
				$('#url').rules('remove','remote');
				$('#url').rules('add',{
					remote:nUI.validateRules['CPageForm'].rules.url.remote					
				});
			}
			$('#create_time').val(page.create_time);			
		}
		$('#btn-c-add').show();
	};
	window.add_next_page = function(){
		nUI.closeAjaxDialog();
		$('#btn-c-add').click();
		return false;
	};	
	window.modify_current_page = function(){
		nUI.closeAjaxDialog();	
		$('#btn-c-add').hide();	
		return false;
	};
</script>