<div class="row">		                          
   <form name="{$formName}"                          		
   		data-widget="nuiValidate" action="{$p_url}" 
   		method="post" id="{$formName}-form" target="ajax">   		              	
		<fieldset>							
			{$widgets|render}
		</fieldset>
		<div class="row">
			<div class="btn-group">							
				<button type="submit" class="btn btn-primary">
					保存
				</button>
				<button type="reset" class="btn btn-default">
					重置
				</button>
			</div>
		</div>
	</form>
</div>