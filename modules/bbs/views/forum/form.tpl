<div class="panel panel-default">   
	<div class="panel-body no-padding">
	   <form name="{$formName}"                          		
	   		data-widget="nuiValidate" action="{'bbs/forum/save'|app}" 
	   		method="post" id="{$formName}-form" target="ajax" class="smart-form">   		              	
			<fieldset>							
				{$widgets|render}
			</fieldset>
			<footer class="text-right">						
				<button type="submit" class="btn btn-primary">
					保存
				</button>
				<button type="reset" class="btn btn-default">
					重置
				</button>
			</footer>
		</form>
	</div>
	<script type="text/javascript">
	nUI.validateRules['{$formName}'] = {$rules};
	</script>
</div>