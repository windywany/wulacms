<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-columns"></i> 页面布局			
		</h1>
	</div>	
</div>

<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="well no-padding">
				<form name="KsWidgetForm" data-widget="nuiValidate" action="{'system/layout/add'|app}" method="post" class="smart-form" target="ajax">
					<fieldset>
					{$newRender|render}
					</fieldset>					
				</form>
			</div>
		{foreach $containers as $cid => $container}
			<div class="jarviswidget" id="wid-{$cid}-layout" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-deletebutton="false"
				data-widget-fullscreenbutton="false" data-widget-custombutton="false" data-widget-collapsed="true" data-widget-sortable="false">
                <header>
                     <span class="widget-icon">
                          <i class="fa fa-edit"></i>
                     </span>
                     <h2> {$container[1]}</h2>
                </header>
                <div role="content">
                     <div class="widget-body">                         				

              			<div id="accordion-{$cid}" class="panel-group smart-accordion-default">
							{foreach $widgets[$cid] as $widget}
							<div class="panel panel-default">
								<div class="panel-heading">
									<h6 class="panel-title">
										<a class="collapsed" href="#{$widget['widget']->getID()}" data-parent="#accordion-{$cid}" data-toggle="collapse"> 
										 <i class="fa fa-lg fa-angle-down pull-right"></i> 
										 <i class="fa fa-lg fa-angle-up pull-right"></i> {$widget['widget']->getName()} </a>
									</h6>
								</div>
								<div class="panel-collapse collapse" id="{$widget['widget']->getID()}">
									<div class="panel-body no-padding">										
										<form name="{$cid}Form" action="{'system/layout'|app}" method="post" class="smart-form" target="ajax">
										<input type="hidden" name="page" value="{$page_id}"/>																			
										<fieldset>
										{$widget['widget']->getConfigFormRender()|render}
										</fieldset>
										<footer>								
											<button type="submit" class="btn btn-primary">保存</button>
											<a href="{'system/layout/del'|app}{$widget['widget']->getID()}" target="ajax" data-confirm="你确定要删除这个小部件吗?" class="btn btn-danger">删除</a>									
										</footer>
										</form>
									</div>
								</div>
							</div>
							{/foreach}							
						</div>
	                </div>
	           </div>
           </div>
        {/foreach}
		</article>
	</div>
</section>
<script type="text/javascript">
	nUI.validateRules['KsWidgetForm'] = {$rules};	
</script>