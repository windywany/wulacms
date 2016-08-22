<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-list-ul"></i> {$blockName}
			<span>&gt; 编辑区块内容</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a id="goback"  class="btn btn-default btn-labeled" href="#{'cms/blockitem'|app:0}{$block}">
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
                id="wid-block-w"     
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
                     <h2> 区块内容编辑器 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="BlockItemForm"                          		
                          		data-widget="nuiValidate" action="{'cms/blockitem/save'|app}" 
                          		method="post" id="blockitem-form" class="smart-form" target="ajax"
                          		>
                          	<input type="hidden" name="id" value="{$id}"/>
                          	<input type="hidden" name="block" value="{$block}"/>
                          	<input type="hidden" name="refid" value="{$refid}"/>
							<fieldset>
								<div class="row">
									<section class="col col-10">
										<label class="label">标题</label>
										<label class="input">
										<input type="text" name="title" 
											id="title" value="{$title}"/>
										</label>
									</section>
									<section class="col col-2">
										<label class="label">绑定页面</label>
										<label class="input input-file" for="page_id">
											<div class="button" target="dialog"
												 dialog-title="选择页面"
												 dialog-model="true"
												 dialog-width="450"
												 data-url="{'cms/page/browsedialog'|app}1" 
												 data-for="#page_id">选择</div>
											<input type="text" name="page_id" id="page_id" value="{$page_id}"/>
										</label>
									</section>
								</div>
								
								<section>
									<label class="label">URL</label>
									<label class="input">
									<input type="text" name="url" 
										id="url" value="{$url}"/>
									</label>
								</section>									
								
								<section>
									<label class="label">图片</label>									
									<label class="input input-file" for="image">
										<div class="button" id="uploadImg" data-widget="nuiAjaxUploader" for="#image" data-water="0">
											<i class="fa fa-lg fa-cloud-upload"></i>
										</div>
										<a for="image" class="button" {if $image}href="{$image|media}" rel="superbox[image]"{else}href="javascript:;" style="display:none"{/if}><i class="fa fa-lg fa-eye txt-color-blue"></i></a>
										<input type="text" name="image" id="image" value="{$image}" />										
									</label>
								</section>
								{if $widgets}								
								{$widgets|render}
								{/if}
								<div class="row">	
									<section class="col col-2">
										<label class="label">显示排序</label>
										<label class="input">
											<input type="text" name="sort" id=""sort"" value="{$sort}"/>
										</label>
									</section>		
								</div>					
								<section>
									<label class="label">说明</label>
									<label class="textarea">
										<textarea rows="4" name="description" id="description">{$description|escape}</textarea>
									</label>
								</section>
							</fieldset>
							
							<footer>								
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								<a id="goback"  class="btn btn-default" href="#{'cms/blockitem'|app}{$block}">
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
	nUI.validateRules['BlockItemForm'] = {$rules};
	if(!window.fillBlockFromPage){
		window.fillBlockFromPage = function(id,rows){
			if(rows.length > 0){
				var row = $(rows[0]);
				$('#page_id').val(row.val());
				$('#url').val(row.data('url'));
				$('#title').val(row.data('text'));
				if(row.data('img')){
					$("#image").val(row.data('img'));
				}
			}
		};
	}
	$('#page_id').data('onInsert',fillBlockFromPage);
</script>
