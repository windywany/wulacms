<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-group"></i> 角色管理
			<span>&gt; 新增角色</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a id="btn-rtn-role" class="btn btn-default btn-labeled" href="#{'system/role'|app:0}{$type}">
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
                     <h2> 角色编辑器 </h2>                     
                </header>
                <div>
                     <div class="widget-body widget-hide-overflow no-padding">
                          
                          <form name="UserRoleForm"                          		
                          		data-widget="nuiValidate" action="{'system/role/save'|app}" 
                          		method="post" id="role-form" class="smart-form" target="ajax"
                          		>
                          	<input type="hidden" name="role_id" value="{$role_id}"/>
							<fieldset>												
								<div class="row">
									<section class="col col-3">
										<label class="label">角色类型</label>										
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
									<section class="col col-3">
										<label class="label">角色名</label>
										<label class="input">
										<input type="text" name="role_name" 
											id="role_name" value="{$role_name}"/>
										</label>
									</section>
									<section class="col col-3">
										<label class="label">ID</label>
										<label class="input">
										<input type="text" name="role" 
											id="role" value="{$role}" />
										</label>
									</section>
									<section class="col col-3">
										<label class="label">权重</label>
										<label class="input">
										<input type="text" name="priority" 
											id="priority" value="{$priority}" />
										</label>
										<div class="note">当一个用户拥有多个角色时,授权时使用权重高的角色权限.</div>
									</section>
								</div>								
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
	nUI.validateRules['UserRoleForm'] = {$rules};
</script>