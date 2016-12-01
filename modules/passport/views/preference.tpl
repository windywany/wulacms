<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-cog"></i> 系统设置
			<span>&gt; 通行证设置</span>			
		</h1>
	</div>	
</div>

<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="jarviswidget"
                id="wid-passport-{$_g}-preference"     
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
                     <h2> 通行证设置 </h2>  
                     <ul class="nav nav-tabs pull-right in">
                     	{foreach $groups as $key => $g}
						<li {if $key == $_g}class="active"{/if}>
							<a href="{'passport/preference'|app}?_g={$key}" target="tag" data-tag="#content">
								<i class="fa fa-fw fa-cog"></i> 
								<span class="hidden-mobile hidden-tablet">{$g}</span>
							</a>
						</li>
						{/foreach}										
					 </ul>                   
                </header>
                <div>
                     <div class="widget-body no-padding">                          
                          <form name="{$formName}"                          		
                          		data-widget="nuiValidate" action="{'passport/preference'|app}" 
                          		method="post" id="passport-preference-form" class="smart-form" target="ajax">
                          		<input type="hidden" name="_g" value="{$_g}"/>                      	
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