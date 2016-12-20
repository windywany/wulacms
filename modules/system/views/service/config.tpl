<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-suitcase"></i> "{$gname}"服务配置
		</h1>
	</div>
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <div class="pull-right margin-top-5 margin-bottom-5">
            <a class="btn btn-default"
               href="#{'system/group'|app:0}{$gtype}"><i class="glyphicon glyphicon-circle-arrow-left"></i> 返回 </a>
        </div>
    </div>
</div>

<section id="widget-grid">	
	<div class="row">		
		<article class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body no-padding">
					<ul class="nav nav-tabs in" id="role-type-tab">
						<li {if $service==''}class="active"{/if}>
							<a href="#{'system/service/config'|app:0}{$gid}" class="txt-color-blue"><i class="fa fa-suitcase"></i> <span class="hidden-mobile hidden-tablet">服务列表</span></a>
						</li>
						{foreach $configs as $t=>$tname}
						<li {if $t==$service}class="active"{/if}>
							<a href="#{'system/service/config'|app:0}{$gid}/{$t}" class="txt-color-purple"><i class="fa fa-cog"></i> <span class="hidden-mobile hidden-tablet">{$tname}</span></a>
						</li>
						{/foreach}
					</ul>
                    <form name="{$formName}"
                          data-widget="nuiValidate" action="{'system/service/savecfg'|app}"
                          method="post" id="{$formName}-form" class="smart-form" target="ajax">
                        <input type="hidden" name="gid" value="{$gid}"/>
                        <input type="hidden" name="service" value="{$service}"/>
                        <fieldset>
                            {$widgets|render}
                        </fieldset>
                        <footer>
                            <button type="submit" class="btn btn-primary">
                                保存
                            </button>
                            <button type="reset" class="btn btn-default">
                                重置
                            </button>
                        </footer>
                    </form>
                </div>
			</div>
		</article>
	</div>
    <script type="text/javascript">
		nUI.validateRules['{$formName}'] = {$rules};
    </script>
</section>