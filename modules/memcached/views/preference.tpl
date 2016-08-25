<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-cog"></i> 系统设置
			<span>&gt; cache设置</span>			
		</h1>
	</div>	
</div>

<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="jarviswidget"
                id="wid-mem-preference"     
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
                     <h2> cache设置 </h2>                     
                </header>
                <div>
                     <div class="widget-body no-padding">                          

                          <form name="MemPreferenceForm"                          		
                          		data-widget="nuiValidate" action="{'memcached'|app}" 
                          		method="post" id="preference-form" class="smart-form" target="ajax">                          	
							<fieldset>
								{if $errorTip}
								<section>
									<div class="alert alert-warning">										
										<i class="fa-fw fa fa-info"></i>
										<strong>Warning!</strong> {$errorTip}
									</div>
								</section>	
								{/if}													
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
	nUI.validateRules['MemPreferenceForm'] = {$rules};	
</script>