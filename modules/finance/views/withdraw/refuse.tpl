<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h5 class="txt-color-blueDark">
			<i class="fa-fw fa fa-sitemap"></i> {$channelType}
			<span>&gt; 提现拒绝</span>
		</h5>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a  class="btn btn-default" href="#{'finance/withdraw/0'|app:0}" id="rtnbtn">
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
                     <h2> 拒绝理由 </h2>
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
						 <form name="{$formName}"
							   data-widget="nuiValidate" action="{'finance/withdraw/refuse/'|app}{$wid}"
							   method="post" id="{$formName}-form" class="smart-form" target="ajax"
						 >

							<fieldset>
                                <input type="hidden" name="wid"  id="wid" value="{$wid}" />
								<div class="row">
									<section class="col col-12">
										<label class="label">选择拒绝理由</label>
										<label class="select">
											<select name="op" id="op">
                                                <option value="rename">未实名认证</option>
                                                <option value="reopenid">openid异常</option>
											</select>
											<i></i>
										</label>
									</section>
								</div>
							</fieldset>

							<footer>
								<button type="submit" class="btn btn-primary">
									保存
								</button>
								{*<a  class="btn btn-default" href="#{'finance/withdraw/0'|app:0}">*}
									{*返回*}
								{*</a>*}
							</footer>
						</form>

                     </div>
                </div>
           </div>
		</article>
	</div>
</section>
