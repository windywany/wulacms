<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 hidden-xs hidden-sm">
		<h1 class="txt-color-blueDark">
			<i class="fa-fw fa fa-user"></i> 会员管理
			<span>&gt; {if $mid}编辑{else}新增{/if}</span>			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		<div class="pull-right margin-top-5 margin-bottom-5">
			<a id="btn-rtn-member" class="btn btn-default btn-labeled" href="#{'passport/members'|app:0}">
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
                id="wid-member-form-1"     
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
	                <h2> 会员资料 </h2>
	                <ul class="nav nav-tabs pull-right in">
						<li class="active">
							<a href="#base-member-info" data-toggle="tab">
								<i class="fa fa-user"></i> 
								<span class="hidden-mobile hidden-tablet">基本信息</span>
							</a>
						</li>						
					</ul>                    
                </header>
                <div>
                 <div class="widget-body widget-hide-overflow no-padding">
                      <form class="smart-form tab-content" name="MemberModelForm" data-widget="nuiValidate" action="{'passport/members/save'|app}" method="post" id="member-form" target="ajax">
                      	<input type="hidden" name="mid" value="{$mid}"/>                      	
                      	<input type="hidden" name="otype" value="{$type}"/>  
                      	<input type="hidden" name="role_id" value="0"/>  
						<div id="base-member-info" class="tab-pane active">
							<fieldset>
								<div class="row">
									<section class="col col-3">
										<label class="label">类型</label>
										<label class="select">
											<select name="type" id="type">
												{html_options options=$types selected={$type}}
											</select>
											<i></i>
										</label>
										<div class="note">警告:修改会员类型需要重新认证。</div>
									</section>
									<section class="col col-3">
										<label class="label">会员组</label>
										<label class="select">
											<select name="group_id" id="group_id">
												{html_options options=$groups selected="{$group_id}"}
											</select>
											<i></i>
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
											<label class="radio">
												<input {$status|checked:'2'} type="radio" name="status" value="2"/>
												<i></i>未激活</label>	
										</div>
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
								<div class="row">									
									<section class="col col-3">
										<label class="label">账户</label>
										<label class="input">
										<i class="icon-append fa fa-user"></i>
										<input type="text" name="username" 
											id="username" value="{$username}"/>
										</label>
									</section>
									<section class="col col-3">
										<label class="label">昵称</label>
										<label class="input">
										<i class="icon-append fa fa-user"></i>
										<input type="text" name="nickname" 
											id="nickname" value="{$nickname}"/>
										</label>
									</section>
									<section class="col col-3">
										<label class="label">邮箱</label>
										<label class="input">
										<i class="icon-append fa fa-envelope-o"></i>
										<input type="text" name="email" 
											id="email" value="{$email}"/>
										</label>
									</section>
									<section class="col col-3">
										<label class="label">手机</label>
										<label class="input">
										<i class="icon-append fa fa-envelope-o"></i>
										<input type="text" name="phone" id="phone" value="{$phone}"/>
										</label>
									</section>
								</div>								
								{if $enable_invation}	
								<div class="row">
									<section class="col col-3">
										<label class="label">推荐码</label>
										<label class="input">										
										<input type="text" value="{$recommend_code}" id="recommend_code" name="recommend_code"/>
										</label>
										<div class="note">用于邀请其他会员注册</div>
									</section>	
									<section class="col col-3">
										<label class="label">邀请码</label>
										<label class="input">										
										<input type="text" name="invite_code" 
											id="invite_code" value="{$invite_code}"/>
										</label>
										<div class="note">注册时填写的推荐码</div>
									</section>
									<section class="col col-3">
										<label class="label">邀请人ID</label>
										<label class="input">										
										<input type="text" name="invite_mid" 
											id="invite_mid" value="{$invite_mid}" readonly="readonly"/>
										</label>
										<div class="note">注册时填写的推荐码对应的会员</div>
									</section>
								</div>
								{/if}
								{if $widgets}{$widgets|render}{/if}								
								<div class="row">									
									<section class="col col-6">
										<label class="label">密码</label>
										<label class="input">
										<i class="icon-append fa fa-lock"></i>					
										<input type="password" name="passwd" 
											id="passwd"/>
										</label>
										{if $mid}
										<div class="note">如不修改请留空.</div>
										{/if}
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
	nUI.validateRules['MemberModelForm'] = {$rules};		
</script>