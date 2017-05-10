<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<h1 class="txt-color-blueDark">
			<i class="fa fa-fw fa-android"></i> 移动端
			<span>&gt; 展示模板编辑器</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default" href="#{'mobiapp/pageview'|app:0}" id="rtn2ads">
				<i class="glyphicon glyphicon-circle-arrow-left"></i> 返回
			</a>			
		</div>
	</div>
</div>

<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="jarviswidget"
                id="wid-mobi-ch-form"     
                data-widget-colorbutton="true"
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
                     <h2> 展示模板编辑器 </h2>
                </header>
                <div>
                     <div class="widget-body no-padding">                          
                          <form name="{$formName}"                          		
                          		data-widget="nuiValidate" action="{'mobiapp/pageview/save'|app}" 
                          		method="post" id="{$formName}-form" class="smart-form" target="ajax">                          		              	
							<fieldset>
								{$widgets|render}								
							</fieldset>
							<footer>
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								<button type="reset" class="btn btn-default">
									重置
								</button>
							</footer>							
						</form>
                     </div>
                </div>
           </div>
		</article>
	</div>
</section>
<script type="text/javascript">    
	nUI.validateRules['{$formName}'] = {$rules};	
</script>