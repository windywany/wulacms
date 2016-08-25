<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-list-ul"></i> 区块管理
			<span>&gt; 编辑区块</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default btn-labeled" id="btn-rtn" href="#{'cms/block'|app:0}">
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
                     <h2> 区块编辑器 </h2>                    
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="BlockForm"                          		
                          		data-widget="nuiValidate" action="{'cms/block/save'|app}" 
                          		method="post" id="block-form" class="smart-form" target="ajax"
                          		>
                          	<input type="hidden" name="id" value="{$id}"/>
							<fieldset>												
								<div class="row">
									<section class="col col-4">
										<label class="label">分类（页面）</label>
										<label class="select">
											<select name="catelog" id="catelog">
												{html_options options=$options selected=$catelog}
											</select>
											<i></i>
										</label>
									</section>
									<section class="col col-8">
										<label class="label">区块名</label>
										<label class="input">
										<input type="text" name="name" 
											id="name" value="{$name}"/>
										</label>
									</section>									
								</div>
								<section>
									<label class="label">引用ID</label>
									<label class="input">
									<input type="text" name="refid" 
										id="refid" value="{$refid}" {if $id}readonly="readonly"{/if}/>
									</label>
								</section>								
								<section>
									<label class="label">说明</label>
									<label class="textarea">
										<textarea rows="4" name="note" id="note">{$note|escape}</textarea>
									</label>
								</section>
							</fieldset>
							
							<footer>								
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								<a  class="btn btn-default" href="{'cms/block'|app}" 
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
	nUI.validateRules['BlockForm'] = {$rules};
</script>