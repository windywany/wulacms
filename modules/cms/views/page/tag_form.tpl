<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-copy"></i> {$pageTypeName}管理
			<span>&gt; 编辑{$modelName}</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default btn-labeled" href="#{'cms/page/my'|app:0}">
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
                id="wid-page-form"     
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
                     <h2> {$modelName}编辑器 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="PageForm"                          		
                          		data-widget="nuiValidate" action="{'cms/page/save'|app}" 
                          		method="post" id="page-form" class="smart-form" target="ajax"
                          		style="position: relative;"
                          		>
                          	<input type="hidden" name="id" id="id" value="{$id}"/>
                          	<input type="hidden" name="model" value="{$model}"/>
                          	<input type="hidden" name="page_type" value="page"/>
                          	<input type="hidden" id="create_time" name="create_time" value="{$create_time}"/>
                          	<input type="hidden" id="okeywords" name="okeywords" value="{$keywords}"/>
                          	<input type="hidden" name="title_color" id="title_color" value="{$title_color}"/>
                          	<input type="hidden" name="url_key" id="url_key" value="{$url_key}"/>
							<fieldset>												
								<div class="row">
									<section class="col col-8">
										<label class="label">标题</label>
										<label class="input">
											<i class="icon-append fa fa-square" 
											data-plugin="colorpicker"
											data-widget="nuiKindEditor"
											data-for="#title_color"></i>
											<input type="text" name="title" id="title" value="{$title|escape}"/>
										</label>					
									</section>									
									<section class="col col-4">
										<label class="label">短标题</label>
										<label class="input">
											<input type="text" name="title2" id="title2" value="{$title2|escape}"/>
										</label>
									</section>
								</div>
								<div class="row">
									<section class="col col-6">
										<label class="label">缩略图</label>
										<label class="input input-file" for="image">
											<div class="button" id="uploadImg" data-widget="nuiAjaxUploader" for="#image" data-water="0">
												<i class="fa fa-lg fa-cloud-upload"></i>
											</div>
											<a for="image" class="button" {if $image}href="{$image|media}" rel="superbox[image]"{else}href="javascript:;" style="display:none"{/if}><i class="fa fa-lg fa-eye txt-color-blue"></i></a>
											<input type="text" name="image" id="image" value="{$image}" />
										</label>
									</section>	
									<section class="col col-3">
										<label class="label">栏目</label>
										<label class="select">
											<select name="channel" id="channel">
												{html_options options=$options selected=$channel}
											</select>
											<i></i>
										</label>
									</section>	
									<section class="col col-3">
										<label class="label">绑定碎片</label>
										<input type="hidden" data-widget="nuiCombox"
										 style="width:99%"
										 data-source="{'cms/chunk/auto_chunk'|app}" 
										 value="{$chunk}" id="chunk" name="chunk"/>
									</section>							
								</div>								
								<div class="row">
									<section class="col col-4">
										<label class="label">关键词(以','分开.)</label>
										<label class="textarea textarea-expandable">
										<textarea 
										class="custom-scroll" 
										name="keywords" rows="3" 
										id="keywords">{$keywords}</textarea>
										</label>										
									</section>
									<section class="col col-8">
										<label class="label">页面描述</label>
										<label class="textarea textarea-expandable">
										<textarea class="custom-scroll" name="description" rows="3" id="description">{$description|escape}</textarea>
										</label>										
									</section>
								</div>
								{if $cwidgets}								
								{$cwidgets|render}
								{/if}
								{if $widgets}								
								{$widgets|render}
								{/if}
								<section>
									<div class="inline-group">
										{if $gkeywords}
										<label class="checkbox">
											<input type="checkbox" {if $gkeywords}checked="checked"{/if}  name="gkeywords"/>
											<i></i>自动从标题提取关键词</label>
										{/if}
										<label class="checkbox">
											<input type="checkbox" name="firstimage"/>
											<i></i>提取第一个图片为缩略图</label>										
										{'page_checkbox_options'|fire}
									</div>
								</section>

								<section id="pageEditorWrapper" {if $flag_j}style="display:none"{/if}>
									<textarea id="page_content" name="content" type="text/plain" style="height:100px;width:100%" rows="20" cols="100">
									{$content|escape}
									</textarea> 
								</section>
								<div class="row">									
									<section class="col col-2">
										<label class="label">评论选项</label>
										<label class="checkbox">
											<input type="checkbox" {if $allow_comment}checked="checked"{/if} name="allow_comment"/>
											<i></i>允许评论</label>
									</section>
									<section class="col col-2">
										<label class="label">浏览次数</label>
										<label class="input">
											<input type="text" name="view_count" id="view_count" value="{$view_count}"/>
										</label>
									</section>
									<section class="col col-3">
										<label class="label">发布时间</label>
										<label class="input">
											<i class="icon-append fa fa-calendar"></i>
											<input type="text" name="publish_date" 
											data-widget="nuiDatepicker" 
											id="publish_date" value="{$publish_date}"/>
										</label>							
									</section>
									<section class="col col-2">
										<label class="label">&nbsp;</label>
										<label class="input">
											<i class="icon-append fa fa-clock-o"></i>
											<input type="text" name="publish_time"
											data-widget="nuiTimepicker"
											id="publish_time" value="{$publish_time}"/>
										</label>
									</section>
									<section class="col col-3">
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
								</div>
								<section>
									<label class="label">相关文章</label>
									<label class="input input-file" for="related_pages">
										<div class="button" target="dialog"
											 dialog-title="选择文章"
											 dialog-model="true"
											 dialog-width="450"
											 data-url="{'cms/page/browsedialog'|app}" 
											 data-for="#related_pages">选择</div>
										<input type="text" name="related_pages" id="related_pages" value="{$related_pages}"/>
									</label>
								</section>
								<div class="row">
									<section class="col col-6">
										<label class="label">自定义URL(自动生成时请留空)</label>
										<label class="input">
											<input type="text" id="url" name="url" value="{$url}"/>
											<b class="tooltip tooltip-top-left">
												{literal}
												{Y}、{M}、{D} 年月日<br/>
												{timestamp} INT类型的UNIX时间戳<br/>
												{aid} 文章ID<br/>
												{pinyin}、{py} 拼音、拼音首母<br/>
												{typedir} 栏目目录<br/>
												{path} 全路径<br/>
												{rpath} 退一格路径（用于二级域名时）<br/>
												{cc} 日期+ID混编后用转换为适合的字母<br/>
												{tid}、{cid} 栏目编号、栏目识别ID<br/>
												{title}、{title2} 标题、短标题
												{/literal}												
											</b>
										</label>
									</section>
									<section class="col col-6">
										<label class="label">自定义模板</label>
										<label class="input" for="template_file">
											<input type="hidden" data-widget="nuiCombox" style="width:100%"	data-source="{'system/ajax/tpl'|app}?n=1"
												   name="template_file" id="template_file" value="{$template_file}"/>
											</label>
									</section>
								</div>
								
							</fieldset>
							
							<footer id="handy-btns">								
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								{if !$id}
								<a style="display:none" class="btn btn-success" href="{'cms/page/add/page'|app}{$model}/$channel$" target="tag" data-tag="#content" id="btn-c-add">
									再建一篇
								</a>
								{/if}
								<a class="btn btn-default" href="#{'cms/page/my'|app:0}">
									返回
								</a>
							</footer>							
						</form>
                     </div>
                </div>
           </div>
		</article>
		<script type="text/javascript">	
			nUI.validateRules['PageForm'] = {$rules};
			var wrapper = $('#pageEditorWrapper'),ftbtn=$('#handy-btns'),ftbtn1=null;
				
			$(window).scroll(function(){
				var etop = wrapper.offset().top,ht = wrapper.outerHeight(),ftop=ftbtn.offset().top,fw=ftbtn.width();
				var wtop = $(window).scrollTop()+$(window).height();
				if((etop+ht) < wtop && wtop < ftop){
					if(!ftbtn1){
						ftbtn1 = ftbtn.clone();
						ftbtn1.insertAfter(ftbtn);
					}
					ftbtn1.css('position','fixed').css({
					'bottom':0,'right':15,width:fw
					});
				}else{
					if(ftbtn1){
						ftbtn1.remove();
						ftbtn1 = null;
					}
				}
			});		
			$('input[name=flag_j]').change(function(){ 
				if($(this).prop('checked')){
					$('#redirectWrapper').show();
					$('#pageEditorWrapper').hide();
				}else{
					$('#pageEditorWrapper').show();
					$('#redirectWrapper').hide();
				}
			});	
			var pageEditor = KindEditor.create('#page_content',{ 
				basePath:"{'kindeditor'|assets}/",
				resizeType:1,
				filePostName:'file',
				pagebreakHtml:'[page]',
				uploadJson:KissCms.AjaxUploadURL + "?{$_SessionName}={$_SessionID}",
				fillDescAfterUploadImage:false,
				imageTabIndex:1,
				cssData:'{$editor_css}',
				items:['source','|', 'undo', 'redo', '|', 'justifyleft', 'justifycenter', 'justifyright',
				'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent','|',
				,'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
				'italic', 'underline', 'strikethrough','|', 'image', 'multiimage','|','link', 'unlink','|','pagebreak'{$editor_plugins},'fullscreen'],
				layout:'{$editor_layout}'
			});
			nUI.ajaxCallbacks.pageSaved = function(args){
				var page = args.page;
				if(page){
					if(page.is_new){
						$('#id').val(page.id);
						nUI.validateRules['PageForm'].rules.url.remote = nUI.validateRules['PageForm'].rules.url.remote+page.id;
						$('#url').rules('remove','remote');
						$('#url').rules('add',{
							remote:nUI.validateRules['PageForm'].rules.url.remote					
						});
					}
					$('#create_time').val(page.create_time);
					$('#okeywords').val(page.keywords);
					$('#keywords').val(page.keywords);
					$('#url_key').val(page.url_key);
					$('#url').val(page.url);
					$('#description').val(page.description);
					if(page.image){
						$('#image').val(page.image);
					}
					if(page.content){
						pageEditor.html(page.content);
					}
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
	</div>	
</section>
