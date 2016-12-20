<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-sitemap"></i> 金币类型
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default btn-labeled" href="#{'coins/type'|app:0}">
				<span class="btn-label">
					<i class="glyphicon glyphicon-circle-arrow-left"></i>
				</span> 返回
			</a>
		</div>
	</div>
</div>

<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="jarviswidget"
				 id="wid-{$formName}-preference"
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
					<h2> 金币类型编辑器 </h2>
				</header>
				<div>
					<div class="widget-body no-padding">
						<form name="{$formName}"
							  data-widget="nuiValidate" action="{'coins/type/save'|app}"
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
					</div>
				</div>
			</div>
		</article>
	</div>
</section>
<script type="text/javascript">
	nUI.validateRules['{$formName}'] = {$rules};
</script>