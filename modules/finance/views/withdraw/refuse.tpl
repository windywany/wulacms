<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h5 class="txt-color-blueDark">
			<i class="fa-fw fa fa-sitemap"></i> {$channelType}
			<span>&gt; 提现拒绝</span>
		</h5>
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
						 <form name="{$formName}" data-widget="nuiValidate" action="{'finance/withdraw/refuse/'|app}{$wid}" method="post" id="{$formName}-form" class="smart-form" target="ajax">
							<fieldset>
                                <input type="hidden" name="wid"  id="wid" value="{$wid}" />
								<div class="row">
									<section class="col col-8">
										{*<label class="label">选择拒绝理由</label>*}
                                        <div class="group">
                                            <label class="radio"><input id="re_1" type="radio" name="op" checked value="rename"><i></i>未实名认证</label>
                                            <label class="radio"><input id="re_2" type="radio"  name="op" value="reopenid"><i></i>微信openid异常</label>
                                            <label class="radio"><input id="re_2" type="radio"  name="op" value="reother"><i></i>其他</label>
                                        </div>
									</section>
                                    <section class="col col-8">
                                        <label class="label">
                                           补充说明
                                        </label>
                                            <label class="input">
                                                <input type="text" name="note" id="note">
                                        </label>
                                    </section>
								</div>
							</fieldset>

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
