<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-tags"></i>内链库
			<span>&gt; 编辑内链</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a id="rtn2tag" class="btn btn-default" href="#{'cms/tag'|app:0}">
				<i class="glyphicon glyphicon-circle-arrow-left"></i> 返回
			</a>			
		</div>
	</div>
</div>

<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="jarviswidget"
                id="wid-tag-w"     
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
                     <h2> 内链编辑器 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="TagForm"                          		
                          		data-widget="nuiValidate" action="{'cms/tag/save'|app}" 
                          		method="post" id="tag-form" class="smart-form" target="ajax"
                          		>
                          	<input type="hidden" name="id" value="{$id}"/>
							<fieldset>		
								<div class="row">				
									<section class="col col-4">
										<label class="label">标签</label>
										<label class="input">									
											<input type="text" name="tag" id="tag" value="{$tag}" />
										</label>
									</section>
									<section class="col col-8">
										<label class="label">标题</label>
										<label class="input">
										<input type="text" name="title" 
											id="title" value="{$title}"/>
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
								
							</fieldset>
							
							<footer>								
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								<a class="btn btn-default" href="#{'cms/tag'|app:0}">
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
	nUI.validateRules['TagForm'] = {$rules};
</script>