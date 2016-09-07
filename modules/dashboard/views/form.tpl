<div class="row">
	<div class="col-md-4 hidden-sm">
		<h1 class="txt-color-blueDark">
			{block 'title'}{/block}
		</h1>
	</div>
	<div class="col-sm-12 col-md-8">
		<div class="pull-right margin-top-5 margin-bottom-5">{block toolbar}{/block}</div>
	</div>
</div>
<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="panel panel-default">
				{block form}
				<form name="{$formName}"             		
                      data-widget="nuiValidate" action="{$formUrl}" 
                      method="post" id="{$formName}-form" class="smart-form" target="ajax">
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
					</footer>							
				</form>
				<script type="text/javascript">
					nUI.validateRules['{$formName}'] = {$rules};
				</script>
				{/block}	
				{block js}{/block}	
			</div>
		</article>
	</div>
</section>