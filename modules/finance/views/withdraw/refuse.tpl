<div class="no-padding">
						 <form name="{$formName}" data-widget="nuiValidate" action="{'finance/withdraw/refuse/'|app}{$wid}" method="post" id="{$formName}-form" class="smart-form" target="ajax">
							<fieldset>
                                <input type="hidden" name="wid"  id="wid" value="{$wid}" />
									<section>
										{*<label class="label">选择拒绝理由</label>*}
                                        <div class="group">
                                            <label class="radio"><input id="re_1" type="radio" name="op" checked value="rename"><i></i>未实名认证</label>
                                            <label class="radio"><input id="re_2" type="radio"  name="op" value="reopenid"><i></i>微信openid异常</label>
                                            <label class="radio"><input id="re_2" type="radio"  name="op" value="reother"><i></i>其他</label>
                                        </div>
									</section>
                                    <section >
                                        <label class="label">
                                           补充说明
                                        </label>
                                            <label class="input">
                                                <input type="text" name="note" id="note">
                                        </label>
                                    </section>
							</fieldset>

							<footer>
								<button type="submit" class="btn btn-primary">
									保存
								</button>
							</footer>
						</form>

                     </div>
