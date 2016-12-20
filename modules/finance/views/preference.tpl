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
					 </ul>                   
                </header>
                <div>
                     <div class="widget-body no-padding">                          
                          <form name="{$formName}"                          		
                          		data-widget="nuiValidate" action="{$p_url}" 
                          		method="post" id="{$formName}-form" class="smart-form" target="ajax">
                          		<input type="hidden" name="_g" value="{$_g}"/> 
                          		<input type="hidden" name="_hp" id="_hidden_tip"/>                     	
							<fieldset>							
								{$widgets|render}
							</fieldset>
							<footer>								
								<button type="submit" class="btn btn-primary" id="sms-submit">
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
<script type="text/javascript">
	nUI.validateRules['{$formName}'] = {$rules};
	/*
    $('#vendor').off('change');
    $('#vendor').on('change',function(){
        if($(this).val() == ''){
        	$('#_hidden_tip').val('未选择短信通道，系统将不能发送短信!');
        }else{
        	$('#_hidden_tip').val('短信通道切换为'+$(this).find('option:selected').text()+',请配置此通道.');
        }
		$('#sms-submit').click();
    });*/
</script>