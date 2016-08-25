<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-male"></i> 管理账户
			<span>&gt; {if $user_id}新增{else}编辑{/if}账户</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a id="btn-rtn-user"  class="btn btn-default btn-labeled" href="#{'system/user'|app:0}">
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
                id="wid-user-form-1"     
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
	                <h2> 用户编辑器 </h2>
	                <ul class="nav nav-tabs pull-right in">
						<li class="active">
							<a href="#base-user-info" data-toggle="tab">
								<i class="fa fa-user"></i> 
								<span class="hidden-mobile hidden-tablet">基本信息</span>
							</a>
						</li>						
					</ul>                    
                </header>
                <div>
                 <div class="widget-body widget-hide-overflow no-padding">
                      <form class="smart-form tab-content" name="SystemUserForm" data-widget="nuiValidate" action="{'system/user/save'|app}" method="post" id="user-form" target="ajax">
                      	<input type="hidden" name="user_id" value="{$user_id}"/>                      	
						<div id="base-user-info" class="tab-pane active">
							<fieldset>
								<section>
									<label class="label">用户组</label>
									<label class="select">
										<select name="group_id" id="group_id">
											{html_options options=$groups selected="{$group_id}"}
										</select>
										<i></i>
									</label>
								</section>				
								<div class="row">
									<section class="col col-6">
										<label class="label">账户</label>
										<label class="input">
										<i class="icon-append fa fa-user"></i>
										<input type="text" name="username" 
											id="username" value="{$username}"/>
										</label>
									</section>
									<section class="col col-6">
										<label class="label">用户名</label>
										<label class="input">
										<i class="icon-append fa fa-user"></i>
										<input type="text" name="nickname" 
											id="nickname" value="{$nickname}"/>
										</label>
									</section>
								</div>
								<div class="row">
									<section class="col col-6">
										<label class="label">邮箱</label>
										<label class="input">
										<i class="icon-append fa fa-envelope-o"></i>
										<input type="text" name="email" 
											id="email" value="{$email}"/>
										</label>
									</section>
									<section class="col col-6">
										<label class="label">状态</label>
										<div class="inline-group">
											<label class="radio">
												<input {$status|checked:'1'} type="radio" name="status" value="1"/>
												<i></i>正常</label>
											<label class="radio">
												<input {$status|checked:'0'} type="radio" name="status" value="0"/>
												<i></i>禁用</label>
										</div>
									</section>
								</div>						
								<div class="row">
									<section class="col col-6">
										<label class="label">密码</label>
										<label class="input">
										<i class="icon-append fa fa-lock"></i>					
										<input type="password" name="passwd" 
											id="passwd"/>
										</label>
									</section>
									<section class="col col-6">
										<label class="label">确认密码</label>
										<label class="input">
										<i class="icon-append fa fa-lock"></i>
										<input type="password" name="passwd1" 
											id="passwd1"/>
										</label>
									</section>
								</div>
								<div class="row">
									<section class="col col-12">
										<label class="label">角色</label>
										<div class="inline-group">
											{foreach $all_roles as $r}
											<label class="checkbox">
												<input type="checkbox" 
													{$r.role_id|checked:$roles}
													name="roles[]" value="{$r.role_id}"/>
												<i></i>{$r.role_name}</label>
											{/foreach}
										</div>
									</section>
								</div>
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
	nUI.validateRules['SystemUserForm'] = {$rules};	
</script>