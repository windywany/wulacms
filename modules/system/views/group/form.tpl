<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-group"></i> 用户组管理
			<span>&gt; 新增用户组</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a id="btn-rtn-grp" class="btn btn-default btn-labeled" href="#{'system/group'|app:0}{$type}">
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
                     <h2> 用户组编辑器 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="UserGroupForm"                          		
                          		data-widget="nuiValidate" action="{'system/group/save'|app}" 
                          		method="post" id="group-form" class="smart-form" target="ajax"
                          		>
                          	<input type="hidden" name="group_id" value="{$group_id}"/>
                          	<input type="hidden" name="oupid" value="{$upid}"/>
							<fieldset>
								<div class="row">
								<section class="col col-8">
									<label class="label">上级用户组</label>
									<label class="select">
										<select name="upid" id="upid">
											{html_options options=$groups selected=$upid}
										</select>
										<i></i>
									</label>
								</section>
								<section class="col col-4">
									<label class="label">用户组类型</label>
									<label class="select">
										<select name="type" id="type">
											{if $type}
											<option value="{$type}">{$types[$type]}</option>
											{else}
												{html_options selected=$type options=$types}
											{/if}
										</select><i></i>
									</label>
								</section>			
								</div>		
								<section class="row">
									<section class="col col-4">
										<label class="label">组名</label>
										<label class="input">									
										<input type="text" name="group_name" id="group_name" value="{$group_name}"/>
										</label>
									</section>

									<section class="col col-4">
										<label class="label">ID</label>
										<label class="input">
										<input type="text" name="group_refid" d="group_refid" value="{$group_refid}" />
										</label>
									</section>
								</section>
                                <section class="row">
                                    <section class="col col-3">
                                        <label class="label">等级名</label>
                                        <label class="input">
                                            <input type="text" name="rank" id="rank" value="{$rank}"/>
                                        </label>
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">等级</label>
                                        <label class="input">
                                            <input type="text" name="level" id="level" value="{$level}"/>
                                        </label>
                                    </section>
                                    <section class="col col-2">
                                        <label class="label">年费</label>
                                        <label class="input">
                                            <input type="text" name="coins" id="coins" value="{$coins}"/>
                                        </label>
                                    </section>
									<section class="col col-4">
										<label class="label">续费折扣</label>
										<label class="input">
											<input type="text" name="discount" id="discount" value="{$discount}"/>
										</label>
                                        <div class="note">以逗号分隔折扣，如1,0.9,0.7</div>
									</section>
                                </section>
								<section>
									<label class="label">备注</label>
									<label class="textarea">
										<i class="icon-append fa fa-comment"></i>
										<textarea rows="4" name="note" id="note">{$note|escape}</textarea>
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
                </div>
           </div>
		</article>
	</div>
</section>
<script type="text/javascript">
	nUI.validateRules['UserGroupForm'] = {$rules};
</script>