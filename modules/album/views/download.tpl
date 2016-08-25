<form name="DownloadPicForm"                          		
	data-widget="nuiValidate" action="{'album/download'|app}" 
	method="post" id="DownloadPicForm-form" class="smart-form" target="ajax">  
	<input type="hidden" name="id" value="{$id}"/>
	<input type="hidden" name="title" value="{$title}"/>
	<fieldset>
		{$widgets|render}		
	</fieldset>
	<footer>
		<button type="submit" class="btn btn-primary">
			下载 <i class="fa fa-fw fa-check-circle"></i>
		</button>
		<a href="javascript:void(0);" onclick="nUI.closeDialog('download-album-pic')" class="btn btn-default">
			<i class="fa fa-fw fa-times-circle"></i> 取消
		</a>
	</footer>							
</form>
<script type="text/javascript">    
	nUI.validateRules['DownloadPicForm'] = {$rules};	
</script>