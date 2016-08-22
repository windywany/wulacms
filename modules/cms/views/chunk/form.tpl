<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-code"></i> 碎片管理
			<span>&gt; 编辑碎片</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default btn-labeled" href="{'cms/chunk'|app}" 
				target="tag" data-tag="#content">
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
                id="wid-chunk-w"     
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
                     <h2> 碎片编辑器 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="ChunkForm"                          		
                          		data-widget="nuiValidate" action="{'cms/chunk/save'|app}" 
                          		method="post" id="chunk-form" class="smart-form" target="ajax"
                          		>
                          	<input type="hidden" name="id" value="{$id}"/>
							<fieldset>												
								<div class="row">
									<section class="col col-4">
										<label class="label">分类</label>
										<label class="select">
											<select name="catelog" id="catelog">
												{html_options options=$options selected=$catelog}
											</select>
											<i></i>
										</label>
										
									</section>
									<section class="col col-8">
										<label class="label">碎片名</label>
										<label class="input">
										<input type="text" name="name" 
											id="name" value="{$name}"/>
										</label>
									</section>
								</div>
								<section>
									<label class="label">关键词</label>
									<label class="input">
									<input type="text" name="keywords" 
										id="keywords" value="{$keywords}"/>
									</label>
								</section>
								<div class="row">
									<section class="col col-3">
										<label class="checkbox">
											<input type="checkbox" {if $istpl}checked="checked"{/if}  name="istpl"/>
											<i></i>启用模板解析</label>
									</section>
									<section class="col col-3">
										<label class="checkbox">
											<input type="checkbox" {if $inline}checked="checked"{/if}  name="inline"/>
											<i></i>启用内链解析</label>
									</section>
								</div>
								
								<section>
									<label class="label">内容</label>
									<label class="textarea">
										<textarea rows="6" name="html" id="html">{$html|escape}</textarea>
									</label>
								</section>
							</fieldset>
							
							<footer>								
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								<a  class="btn btn-default" href="{'cms/chunk'|app}" 
									target="tag" data-tag="#content">
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
	nUI.validateRules['ChunkForm'] = {$rules};
</script>