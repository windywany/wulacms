<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-copy"></i> {$pageTypeName}管理
			<span>&gt; 编辑{$modelName}</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default btn-labeled" href="#{'album'|app:0}">
				<span class="btn-label">
					<i class="glyphicon glyphicon-circle-arrow-left"></i>
				</span> 返回
			</a>			
		</div>
	</div>
</div>

<section id="widget-gridX">
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
                          		method="post" id="page-form" class="smart-form" target="ajax">
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
										<label class="label">相册标题</label>
										<label class="input">
											<i class="icon-append fa fa-square" 
											data-plugin="colorpicker"
											data-widget="nuiKindEditor"
											data-for="#title_color"></i>
											<input type="text" name="title" id="title" value="{$title|escape}"/>
										</label>					
									</section>									
									<section class="col col-4">
										<label class="label">相册名称</label>
										<label class="input">
											<input type="text" name="title2" id="title2" value="{$title2|escape}"/>
										</label>
									</section>
								</div>								
								<div class="row">
									<section class="col col-4">
										<label class="label">栏目</label>
										<label class="select">
											<select name="channel" id="channel">
												{html_options options=$options selected=$channel}
											</select>
											<i></i>
										</label>
									</section>
									<section class="col col-2">
										<label class="label">标签</label>
										<label class="input">
										<input type="text" value="{$tag|escape}" id="tag" name="tag"/>
										</label>
									</section>	
									<section class="col col-6">
										<label class="label">封面图片</label>
										<label class="input input-file" for="image">
											<div class="button" id="uploadImg" data-widget="nuiAjaxUploader" for="#image" data-water="0">
												<i class="fa fa-lg fa-cloud-upload"></i>
											</div>
											<a for="image" class="button" {if $image}href="{$image|media}" rel="superbox[image]"{else}href="javascript:;" style="display:none"{/if}><i class="fa fa-lg fa-eye txt-color-blue"></i></a>
											<input type="text" name="image" id="image" value="{$image}" />
										</label>
									</section>								
								</div>	
								<div class="row">
									<section class="col col-6">
										<label class="label">相册关键词(以','分开.)</label>
										<label class="textarea textarea-expandable">
											<textarea class="custom-scroll" name="keywords" rows="3" id="keywords">{$keywords}</textarea>
										</label>										
									</section>
									<section class="col col-6">
										<label class="label">相册属性</label>
										<div class="inline-group">
											<label class="checkbox">
												<input type="checkbox" {if $flag_h}checked="checked"{/if}  name="flag_h"/>
												<i></i>头条[h]</label>
											<label class="checkbox">
												<input type="checkbox" {if $flag_c}checked="checked"{/if}  name="flag_c"/>
												<i></i>推荐[c]</label>
											<label class="checkbox">
												<input type="checkbox" {if $flag_a}checked="checked"{/if}  name="flag_a"/>
												<i></i>特荐[a]</label>
											<label class="checkbox">
												<input type="checkbox" {if $flag_b}checked="checked"{/if}  name="flag_b"/>
												<i></i>加粗[b]</label>										
										</div>
									</section>																		
								</div>
								<section>
									<label class="label">相册描述</label>
									<label class="textarea">
									<textarea class="custom-scroll" name="description" rows="3" id="description">{$description|escape}</textarea>
									</label>										
								</section>
								<section>
									<label class="label">上传相片</label>
									<div class="m-ajax-uploador">
										<div for="#album_pics" data-widget="nuiAjaxUploader" data-water="0" data-extensions="jpg,gif,png,jpeg" data-multi-upload="true" id="upload_adea_album_pics" data-name="album_pics" class="up-file add-btn" style="z-index: 0;"></div>
										<b class="clearfix"></b>
									</div>
								</section>
								<div class="row">
									<section class="col col-10">
										<label class="label">远程图片</label>
										<label class="textarea">
											<textarea class="custom-scroll" name="remote_pics" rows="8" id="remote_pics"></textarea>
										</label>
										<div class="note">一行一个远程图片，可使用||将图片地址与图片标题分开.</div>										
									</section>
									<section class="col col-2">
										<label class="label">底部自动去除</label>
										<label class="input">
										<input type="text" id="resize_h" name="resize_h"/>
										</label>
										<div class="note">高度值，单位象素.</div>	
									</section>
								</div>
								{if $widgets}								
								{$widgets|render}
								{/if}								
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
							
							<footer>								
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								{if !$id}
								<a style="display:none" class="btn btn-success" href="{'cms/page/add/page'|app}{$model}/$channel$" target="tag" data-tag="#content" id="btn-c-add">
									再建一篇
								</a>
								{/if}
								<a class="btn btn-default" href="#{'album'|app:0}">
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