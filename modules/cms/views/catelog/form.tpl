<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-folder-open"></i> {$catelogTitle}
			<span>&gt; 编辑器</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default btn-labeled" href="{'cms/catelog'|app}{$catelogType}" 
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
                id="wid-catelog-form"     
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
                     <h2> {$catelogTitle}编辑器 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="CatelogForm"                          		
                          		data-widget="nuiValidate" action="{'cms/catelog/save'|app}" 
                          		method="post" id="model-form" class="smart-form" target="ajax"
                          		>
                          	<input type="hidden" name="id" value="{$id}"/>
                          	<input type="hidden" name="type" value="{$catelogType}"/>
							<fieldset>											
								<div class="row">
									<section class="col col-4">
										<label class="label">上级{$catelogTitle}</label>
										<label class="select">
											<select name="upid" id="upid">
												{html_options options=$options selected="{$upid}"}
											</select>
											<i></i>
										</label>
									</section>	
									<section class="col col-4">
										<label class="label">{$catelogTitle}</label>
										<label class="input">									
										<input type="text" name="name" 
											id="name" value="{$name}"/>
										</label>
									</section>
									<section class="col col-4">
										<label class="label">别名</label>
										<label class="input">									
										<input type="text" name="alias" 
											id="alias" value="{$alias}"/>
										</label>
									</section>									
								</div>				
								<section>
									<label class="label">说明</label>
									<label class="textarea">
										<i class="icon-append fa fa-comment"></i>
										<textarea rows="4" 
											name="note" id="note">{$note|escape}</textarea>
									</label>
								</section>
							</fieldset>
							
							<footer>
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								<a  class="btn btn-default" href="{'cms/catelog'|app}{$catelogType}" 
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
	nUI.validateRules['CatelogForm'] = {$rules};
</script>