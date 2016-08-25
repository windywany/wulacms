<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-globe"></i> 站点管理
			<span>&gt; 编辑站点</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default btn-labeled" id="rtn2site" href="#{'msite'|app:0}">
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
                id="wid-id-site"     
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
                     <h2> 站点编辑器 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="MSiteForm"                          		
                          		data-widget="nuiValidate" action="{'msite/save'|app}" 
                          		method="post" id="navi-form" class="smart-form" target="ajax">
                          	<input type="hidden" id="id" name="id" value="{$id}"/> 
                          	<input type="hidden" name="oldd" value="{$domain}"/> 
                          	<input type="hidden" name="oldmd" value="{$mdomain}"/>  	
							<fieldset>
								<div class="row">									
									<section class="col col-4">
										<label class="label">二级域名</label>
										<label class="input">									
										<input type="text" name="domain" id="domain" value="{$domain}"/>
										</label>
									</section>
									
									<section class="col col-2">
										<label class="label">模板主题</label>
										<label class="select">
											<select name="theme" id="theme">
												{html_options options=$themes selected=$theme}
											</select>
											<i></i>
										</label>										
									</section>
									<section class="col col-4">
										<label class="label">移动域名</label>
										<label class="input">									
										<input type="text" name="mdomain" id="mdomain" value="{$mdomain}"/>
										</label>
									</section>
									
									<section class="col col-2">
										<label class="label">移动模板主题</label>
										<label class="select">
											<select name="mtheme" id="mtheme">
												{html_options options=$themes selected=$mtheme}
											</select>
											<i></i>
										</label>										
									</section>
								</div>
								
								<div class="row">
									<section class="col col-4">
											<label class="label">绑定栏目</label>
											<label class="select">
												<select name="channel" id="channel">
													{html_options options=$channels selected=$channel}
												</select>
												<i></i>
											</label>										
									</section>										
									<section class="col col-8">
										<label class="label">绑定专题栏目</label>
										{if $all_topics}
										<div class="inline-group">
											{foreach $all_topics as $r}
											<label class="checkbox">
												<input type="checkbox" 
													{$r.refid|checked:$topics}
													name="topics[]" value="{$r.refid}"/>
												<i></i>{$r.name}</label>
											{/foreach}
										</div>
										{else}
											<div class="note">无可绑专题栏目</div>
										{/if}
									</section>
								</div>						
							</fieldset>						

							<footer>
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								<a  class="btn btn-default" href="#{'msite'|app:0}">
									返回
								</a>
							</footer>
						</form>

                     </div>
                </div>
           </div>
		</article>
	</div>
</section>
<script type="text/javascript">
	nUI.validateRules['MSiteForm'] = {$rules};	
</script>