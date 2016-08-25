<form name="AlbumPicForm"                          		
	data-widget="nuiValidate" action="{'album/save'|app}" 
	method="post" id="AlbumPicForm-form" class="smart-form" target="ajax">  
	                        		              	
	<fieldset>
		<div class="row">
			<div class="col col-4">
				<div class="media album-prev">
					{if $pic_url}<a class="pull-left" href="javascript:void(0);"> <img class="media-object" src="{$pic_url|media}"> </a>{/if}
				</div>
			</div>
			<div class="col col-8">
				{$widgets|render}
			</div>
		</div>								
	</fieldset>
	
	<footer>
		<button type="submit" class="btn btn-primary">
			保存 <i class="fa fa-fw fa-check-circle"></i>
		</button>
		<a href="javascript:void(0);" onclick="nUI.closeDialog('edit-album-pic')" class="btn btn-default">
			<i class="fa fa-fw fa-times-circle"></i> 取消
		</a>
	</footer>							
</form>
<script type="text/javascript">    
	nUI.validateRules['AlbumPicForm'] = {$rules};	
</script>