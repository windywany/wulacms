<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-list-alt"></i> 模型管理
			<span>&gt; 新增模型</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  id="btn2model" class="btn btn-default btn-labeled" href="#{'cms/model'|app:0}">
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
                id="wid-model-form"     
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
                     <h2> 内容模型编辑器 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="ModelForm"                          		
                          		data-widget="nuiValidate" action="{'cms/model/save'|app}" 
                          		method="post" id="model-form" class="smart-form" target="ajax"
                          		>
                          	<input type="hidden" name="id" value="{$id}"/>
                          	<input type="hidden" name="addon_table" id="addon_table" value="{$addon_table}"/>
							<fieldset>
								<div class="row">
									<section class="col col-4">
										<label class="label">上级模型</label>
										<label class="select">
											<select name="upid" id="upid">
												{html_options options=$models selected=$upid}
											</select><i></i>
										</label>
									</section>
									<section class="col col-2">	
										<label class="label">&nbsp;</label>								
										<label class="checkbox">
											<input type="checkbox" {if $status}checked="checked"{/if}  name="status"/>
											<i></i>启用</label>
									</section>
									<section class="col col-2">	
										<label class="label">&nbsp;</label>									
										<label class="checkbox">
											<input type="checkbox" {if $is_topic_model}checked="checked"{/if}  name="is_topic_model"/>
											<i></i>此模型为专题模型</label>
									</section>
									<section class="col col-2">	
										<label class="label">&nbsp;</label>									
										<label class="checkbox">
											<input type="checkbox" {if $creatable}checked="checked"{/if}  name="creatable"/>
											<i></i>可创建页面</label>
									</section>
									<section class="col col-2">	
										<label class="label">&nbsp;</label>								
										<label class="checkbox">
											<input type="checkbox" {if $is_list_model}checked="checked"{/if}  name="is_list_model"/>
											<i></i>列表显示</label>
									</section>
									
								</div>
												
								<div class="row">
									<section class="col col-4">
										<label class="label">内容模型名称</label>
										<label class="input">									
										<input type="text" name="name" 
											id="name" value="{$name}"/>
										</label>
									</section>
									<section class="col col-2">
										<label class="label">识别ID</label>
										<label class="input">
										<input type="text" name="refid" 
											id="refid" value="{$refid}" {if $id}readonly="readonly"{/if}/>
										</label>
									</section>
									<section class="col col-4">
										<label class="label">编辑表单模板</label>
										<label class="input">									
										<input type="text" name="template" 
											id="template" value="{$template}"/>
										</label>
									</section>
									<section class="col col-2">	
										<label class="label">菜单组</label>									
										<label class="select">
											<select name="role" id="role">
												{html_options options=$groups selected=$role}
											</select><i></i>
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
								<a  class="btn btn-default" href="#{'cms/model'|app:0}">
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
	nUI.validateRules['ModelForm'] = {$rules};
</script>