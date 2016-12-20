<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-sitemap"></i> {$channelType}
			<span>&gt; 添加金币</span>
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default" href="#{'coins/'|app:0}" id="rtnbtn">
				<i class="glyphicon glyphicon-circle-arrow-left"></i> 返回
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
                     <h2> 金币 </h2>
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
						 <form name="{$formName}"
							   data-widget="nuiValidate" action="{'coins/addmoney'|app}"
							   method="post" id="{$formName}-form" class="smart-form" target="ajax"
						 >

							<fieldset>								
								<div class="row">
									<section class="col col-4">
										<label class="label">类型</label>
										<label class="select">
											<select name="type" id="type">
												{foreach $type as $val}
													<option value="{$val.type}">{$val.name}</option>
												{/foreach}
											</select>
											<i></i>
										</label>
									</section>
									<section class="col col-2">
										<label class="label">用户id</label>
										<label class="input">
											<input type="text" name="userid"
												   id="userid" value="{$mid}" />
										</label>
										<div class="note">根据列表用户id</div>
									</section>
									<section class="col col-2">
										<label class="label">金币</label>
										<label class="input">
											<input type="text" name="coins"
												   id="coins" value="" />
										</label>
									</section>
								</div>
							</fieldset>

							<footer>
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								<a  class="btn btn-default" href="#{'coins/'|app:0}">
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
