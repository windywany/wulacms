<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-columns"></i> 模板调用			
		</h1>
	</div>
</div>
<section id="widget-grid">
	<div class="row">
		<article class="col-xs-12 col-md-6">
			<div class="jarviswidget"
                id="wid-cts-form"     
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
                     <h2> 条件编辑器 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="CtsCondForm"                          		
                          		action="{'cms/cts'|app}" 
                          		method="post" id="CtsCondForm-form" class="smart-form" target="ajax">
                          	
							<fieldset>
								<section>
									<label class="label">数据源</label>
									<label class="select">
										<select name="provider" id="provider" target="tag" data-tag="#content" data-url="{'cms/cts'|app}$provider$">
											{html_options options=$providers selected=$provider}
										</select>
										<i></i>
									</label>
								</section>
								{foreach $widgets as $widget}								
								<section>
									<label class="label">{$widget.label}{if $widget.note}({$widget.note}){/if}</label>
									{$widget.widget->render($widget,'')}
								</section>
								{/foreach}												
							</fieldset>
							<footer>								
								<button type="submit" class="btn btn-primary">
									预览数据
								</button>
							</footer>
						</form>

                     </div>
                </div>
           </div>
		</article>
		<article class="col-xs-12 col-md-6">
			<div class="row">
				<article class="col-sm-12">
					<div class="jarviswidget"
		                id="wid-data-preview"     
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
		                     <h2> 数据预览 </h2>                     
		                </header>
		                <div>
		                     <div class="widget-body widget-hide-overflow no-padding">
		                     	<table class="table table-bordered table-striped">
		                     		<thead>
		                     			<tr>
		                     				<th width="80">ID</th>
		                     				<th>数据</th>
		                     			</tr>
		                     		</thead>
		                     		<tbody id="preview-body">
		                     			<tr>
		                     				<td colspan="2">暂无数据</td>
		                     			</tr>
		                     		</tbody>
		                     	</table>
		                     </div>
		                </div>
		           </div>
				</article>
				<article class="col-sm-12">
					<div class="jarviswidget"
		                id="wid-cst-preview"     
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
		                     <h2> 调用标签 </h2>                     
		                </header>
		                <div>
		                     <div class="widget-body widget-hide-overflow">
		                     	<div class="smart-form">
		                     		<label class="textarea">
		                     			<textarea readonly="readonly" id="cts-val" cols="30" rows="3"></textarea>
		                     		</label>
		                     	</div>
		                     </div>
		                </div>
		           </div>
				</article>
				<article class="col-sm-12">
					<div class="jarviswidget"
		                id="wid-data-fields-preview"     
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
		                     <h2> 数据字段</h2>                     
		                </header>
		                <div>
		                     <div class="widget-body widget-hide-overflow">
		                     	<pre id="pre-cts-data-fields" style="display:none;"></pre>
		                     </div>
		                </div>
		           </div>
				</article>
			</div>			
		</article>
	</div>
</section>
<script type="text/javascript">
	nUI.ajaxCallbacks['setPreviewData'] = function(data){
		$('#preview-body').empty().html(data.data);
		$('#cts-val').val(data.cts);
		$('#pre-cts-data-fields').html(data.fields).show();
	};
</script>