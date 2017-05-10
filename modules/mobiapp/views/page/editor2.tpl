<form name="MobiPageForm2"                          		
	data-widget="nuiValidate" action="{'mobiapp/page/save2'|app}" 
	method="post" id="MobiPageForm2-form" class="smart-form" target="ajax">
	<input type="hidden" id="mobi_publish_flag" name="publish_flag" value="0"/>                          		              	
	<fieldset>
		{$widgets|render}								
	</fieldset>
	<footer>
		{if $canPublish}
			<button type="submit" class="btn btn-success" onclick="window.MobiApp.publish(1)">
				<i class="fa fa-fw fa-share-square-o"></i> 立即发布
			</button>
		{/if}
		<button type="submit" class="btn btn-primary" onclick="window.MobiApp.publish(0)">
			<i class="fa fa-fw fa-check-circle"></i> 完成
		</button>		
		{if $rtn}
			<a href="javascript:void(0);" onclick="window.MobiApp.push2mobiapp({$page_id},'{$channel}','{$list_view}')" class="btn btn-info">
			<i class="fa fa-fw fa-arrow-circle-left"></i> 选择栏目与布局样式
			</a>
		{/if}
		<a href="javascript:void(0);" onclick="nUI.closeDialog('mobiapp-edit-mobi-page2')" class="btn btn-default">
			<i class="fa fa-fw fa-times-circle"></i> 取消
		</a>
	</footer>							
</form>
<script type="text/javascript">    
	nUI.validateRules['MobiPageForm2'] = {$rules};	
</script>