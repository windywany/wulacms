<form name="MobiPageForm"                          		
	data-widget="nuiValidate" action="{'mobiapp/page/save'|app}" 
	method="post" id="MobiPageForm-form" class="smart-form" target="ajax">                          		              	
	<fieldset>
		{$widgets|render}								
	</fieldset>
	<footer>
		<button type="submit" class="btn btn-primary">
			下一步 <i class="fa fa-fw fa-arrow-circle-right"></i>
		</button>
		<a href="javascript:void(0);" onclick="nUI.closeDialog('mobiapp-edit-page-form')" class="btn btn-default">
			<i class="fa fa-fw fa-times-circle"></i> 取消
		</a>
	</footer>							
</form>
<script type="text/javascript">    
	nUI.validateRules['MobiPageForm'] = {$rules};	
</script>