<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-user"></i> 个人设置				
		</h1>
	</div>	
</div>

<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12">
			<div class="jarviswidget"
                id="wid-profile-form"     
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
	                <h2> 个人选项编辑器 </h2>
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
                      <form class="smart-form tab-content" name="UserProfileForm" data-widget="nuiValidate" action="{'system/user/profile'|app}" method="post" id="user-form" target="ajax">
                      	<input type="hidden" name="user_id" value="{$user_id}"/>                      	
						<div id="base-user-info" class="tab-pane active">
							<fieldset>
								<div class="row">
									<section class="col col-4">
										<label class="label">控制台主题</label>
										<label class="select">
											<select name="theme" id="theme">
												{html_options options=$themes selected="{$theme}"}
											</select>
											<i></i>
										</label>
									</section>
									<section class="col col-3">
										<label class="label">菜单置顶</label>
										<div class="inline-group">
											<label class="radio">
												<input type="radio" value="0" name="menu_on_top" {if !$menu_on_top}checked="checked"{/if} id="menu_on_top_0"/><i></i>否</label>
											<label class="radio">
												<input type="radio" value="1" name="menu_on_top" {if $menu_on_top}checked="checked"{/if} id="menu_on_top_1"><i></i>是</label>
										</div>
									</section>
									<section class="col col-3">
										<label class="label">固定菜单</label>
										<div class="inline-group">
											<label class="radio">
												<input type="radio" value="0" name="menu_fixed" {if !$menu_fixed}checked="checked"{/if} id="menu_fixed_0"/><i></i>否</label>
											<label class="radio">
												<input type="radio" value="1" name="menu_fixed" {if $menu_fixed}checked="checked"{/if} id="menu_fixed_1"><i></i>是</label>
										</div>
									</section>
								</div>
								<section>
									<label class="label">账户(不可修改)</label>
									<label class="input">
									<i class="icon-append fa fa-user"></i>
									<input type="text" name="username" disabled="disabled" 
										id="username" value="{$username}"/>
									</label>
								</section>
								<section>
									<label class="label">用户名</label>
									<label class="input">
									<i class="icon-append fa fa-user"></i>
									<input type="text" name="nickname" 
										id="nickname" value="{$nickname}"/>
									</label>
								</section>
							
								<section>
									<label class="label">邮箱</label>
									<label class="input">
									<i class="icon-append fa fa-envelope-o"></i>
									<input type="text" name="email" 
										id="email" value="{$email}"/>
									</label>
								</section>									
													
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
	nUI.validateRules['UserProfileForm'] = {$rules};
</script>