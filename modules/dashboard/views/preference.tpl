<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-cog"></i> 系统设置
			<span>&gt; {$title}</span>			
		</h1>
	</div>	
</div>

<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="jarviswidget"
                id="wid-{$formName}-{$_g}-preference"     
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
                     <h2> {$title} </h2>  
                     <ul class="nav nav-tabs pull-right in">
                     	{foreach $groups as $key => $g}
						<li {if $key == $_g}class="active"{/if}>
							<a href="{$p_url}?_g={$key}" target="tag" data-tag="#content">
								<i class="fa fa-fw fa-cog"></i> 
								<span class="hidden-mobile hidden-tablet">{$g}</span>
							</a>
						</li>
						{/foreach}
						{if $customEnabled}
						<li>
							<a href="{'system/preference/custom'|app}{$customEnabled}" 
							   target="dialog" dialog-title="添加配置选项" dialog-width="780" dialog-model="true">
								<i class="fa fa-fw fa-plus"></i> 
								<span class="hidden-mobile hidden-tablet">添加配置</span>
							</a>
						</li>
						{/if}
					 </ul>                   
                </header>
                <div>
                     <div class="widget-body no-padding">                          
                          <form name="{$formName}"                          		
                          		data-widget="nuiValidate" action="{$p_url}" 
                          		method="post" id="{$formName}-form" class="smart-form" target="ajax">
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
								{'get_preference_button'|fire:$_g}									
							</footer>							
						</form>
                     </div>
                </div>
           </div>
		</article>
	</div>
</section>
{if $scripts}
	{foreach $scripts as $st}
	<script type="text/javascript" src="{$st}"></script>
	{/foreach}
{/if}
<script type="text/javascript">
    var customDelURL = "{'system/preference/delf'|app}{$customEnabled}/";
    var customEditURL = "{'system/preference/custom'|app}{$customEnabled}/";
	nUI.validateRules['{$formName}'] = {$rules};	
	nUI.validateRules['CustomCfgFieldForm'] = {$crules};
	var cfields = {$cfields};
	function custom_rest_type_format(type){
		$('#defaults').val('');
		$('#defaults_help').html($(type).attr('title'));
	}
	if(cfields){
		for(var i in cfields){
			var f = cfields[i];
			$('label.label[for="'+ f +'"]').append('[<a href="'+customEditURL+f+'" \
			        target="dialog" dialog-title="添加配置选项" dialog-width="780" dialog-model="true">修改</a>]\
					[<a href="'+customDelURL+f+'" class="txt-color-red" target="ajax" data-confirm="你真的要删除该配置项吗?">删除</a>]');
		}
	}
</script>