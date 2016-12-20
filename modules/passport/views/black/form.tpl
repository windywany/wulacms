<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-user txt-color-red"></i> 昵称黑名单
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a id="btn-rtn-member" class="btn btn-default btn-labeled" href="#{'passport/black'|app:0}">
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
                id="wid-member-form-1"     
                data-widget-colorbutton="false"
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
	                <h2> 黑名单 </h2>
	                <ul class="nav nav-tabs pull-right in">
						<li class="active">
							<a href="#base-member-info" data-toggle="tab">
								<i class="fa fa-user"></i> 
								<span class="hidden-mobile hidden-tablet">昵称黑名单</span>
							</a>
						</li>						
					</ul>                    
                </header>
                <div>
                 <div class="widget-body widget-hide-overflow no-padding">
                      <form class="smart-form tab-content" name="MemberModelForm" data-widget="nuiValidate" action="{'passport/black/save'|app}" method="post" id="member-form" target="ajax">
						<div id="base-member-info" class="tab-pane active">
							<fieldset>
								{$widgets->render()}
							</fieldset>	
						</div>
						<footer>
							<button type="submit" class="btn btn-primary">
								保存
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
	nUI.validateRules['MemberModelForm'] = {$rules};		
</script>