<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-sitemap"></i> {$naviType}
			<span>&gt; 编辑菜单项</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default btn-labeled" href="{'cms/navi'|app}{if $type}{$type}{/if}" 
				target="tag" data-tag="#content">
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
                id="wid-id-1"     
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
                     <h2> 菜单项编辑器 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="NaviMenuForm"                          		
                          		data-widget="nuiValidate" action="{'cms/navi/save'|app}" 
                          		method="post" id="navi-form" class="smart-form" target="ajax"
                          		>
                          	<input type="hidden" id="navi_id" name="id" value="{$id}"/>
                          	<input type="hidden" name="navi" value="{$type}"/>                          	
							<fieldset>
								<div class="row">									
									<section class="col col-4">
										<label class="label">上级菜单项</label>
										<label class="select">
											<select name="upid" id="upid">
												{html_options options=$navis selected=$upid}
											</select>
											<i></i>
										</label>
									</section>
									<section class="col col-8">
										<label class="label">菜单项名称</label>
										<label class="input">										
										<input type="text" name="name" id="name" value="{$name}"/>
										</label>
									</section>
								</div>	
								<div class="row">
									<section class="col col-2">
										<label class="label">打开方式</label>
										<label class="select">
											<select name="target" id="target">
												{html_options options=$targets selected=$target}
											</select>
											<i></i>
										</label>
									</section>
									<section class="col col-2">
										<label class="label">排序</label>
										<label class="input">										
										<input type="text" name="sort" id="sort" value="{$sort}"/>
										</label>
									</section>
									<section class="col col-2">
										<label class="label">&nbsp;</label>
										<div class="inline-group">
											<label class="checkbox">
												<input type="checkbox" name="hidden" {if $hidden}checked="checked"{/if}/>
												<i></i>隐藏此菜单项
											</label>											
										</div>
									</section>
								</div>	
								<div class="row">
									<section class="col col-4">
										<label class="label">绑定到页面</label>
										<label class="input input-file" for="page_id">
										<div class="button" target="dialog"
											 dialog-title="选择文章"
											 dialog-model="true"
											 dialog-width="450"
											 data-url="{'cms/page/browsedialog'|app}" 
											 data-for="#page_id">选择</div>
										<input type="text" name="page_id" id="page_id" value="{$page_id}"/>
									</label>
									</section>
									<section class="col col-8">
										<label class="label">URL</label>
										<label class="input">										
										<input type="text" name="url" id="url" value="{$url}"/>
										</label>
									</section>
								</div>						
							</fieldset>						

							<footer>
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								<a  class="btn btn-default" href="{'cms/navi'|app}{$type}" 
									target="tag" data-tag="#content">
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
	nUI.validateRules['NaviMenuForm'] = {$rules};	
</script>