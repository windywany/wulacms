{include 'passport/views/t1/header.tpl'}
<!-- join form -->
<form id="regist-form" name="registerform" action="{'passport/join'|app}" method="post" class="c_form">
	<input type="hidden" name="_form_id" value="{$_form_id}"/>
	<table class="formtable" cellpadding="0" cellspacing="0">		
		<caption>
			<h2>注册本站帐号</h2>
			<p>请完整填写以下信息进行注册。<br>注册完成后，该帐号将作为您在本站的通行帐号，您可以享受本站提供的各种服务。如果您已经是会员请<a href="{'passport'|app}">登录</a>.</p>
		</caption>

		<tbody>
			<tr>
				<th style="vertical-align: top;" class="fm-label"><span class="noempty">*</span>验证码</th>
				<td>
					<img id="img_seccode" src="{'system/captcha/png/95x30/14'|app}" align="absmiddle">
					<p>请输入上面的4位字母或数字，看不清可<a href="javascript:updateseccode()">更换一张</a></p>
					<input id="captcha" name="captcha" class="fm-text" tabindex="1" autocomplete="off" type="text"/>
				</td>
			</tr>
			<tr>
				<th class="fm-label"><span class="noempty">*</span>电子邮箱</th>
				<td>
					<input id="email" name="email" value="{$user.email}" class="fm-text fm-text-286" tabindex="2" type="text"/>
					<br/>
					请准确填入您的邮箱，在忘记密码，或者您使用邮件通知功能时，会发送邮件到该邮箱。
				</td>
			</tr>	
			<tr>
				<th class="fm-label">昵称</th>
				<td>
					<input id="nickname" name="nickname" class="fm-text fm-text-286" tabindex="3" value="{$nickname}"/>
				</td>
			</tr>		
			<tr>
				<th class="fm-label"><span class="noempty">*</span>登录密码</th>
				<td>
					<input name="passwd" id="passwd" class="fm-text fm-text-286" tabindex="4" type="password"/>					
				</td>
			</tr>

			<tr>
				<th class="fm-label"><span class="noempty">*</span>确认登录密码</th>
				<td>
					<input id="passwd1" name="passwd1" class="fm-text fm-text-286" tabindex="5" type="password"/>
				</td>
			</tr>
			{if $enableInvation}			
			<tr>
				<th width="100">邀请码</th>
				<td>
					<input id="invite_code" name="invite_code" value="{$user.invite_code}" {if $bind_invite_code}readonly="readonly"{/if} class="fm-text fm-text-286" tabindex="6" type="text"/>
					<br/>
					请填写您的推荐人给您的邀请码.
				</td>
			</tr>
			{/if}
			<tr>
				<th>&nbsp;</th>
				<td>			
					<input id="registersubmit" name="registersubmit" value="注册新用户" class="submit" tabindex="7" type="submit"/>
				</td>
			</tr>
			<tr><th>&nbsp;</th><td id="__registerform" style="color:red; font-weight:bold;">{if $error_msg}{$error_msg}{/if}</td></tr>
		</tbody>
	</table>
</form>
{if $enableOAuth}
	{include 'passport/views/t1/oauth.tpl'}
{/if}
<script type="text/javascript">
var invite_required = {$inviteRequired};
var imgSrc = $('#img_seccode').attr('src');
function updateseccode(){
	$('#img_seccode').attr('src',imgSrc+'&_t='+(new Date().getTime()));	
}
var _initValidator = function(){
	$.module.validator.add('#email', [
		'noempty',
		{
			exec:'length',
			args:[5,128],
			error:function(){
				return '不是有效的电子邮箱地址';
			}
		},
		{
			exec:function(){
				return /^(\w+(?:[-+.]\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*)$/i.test($.trim(this.val()));
			},
			error:function(){
				return '不是有效的电子邮箱地址';
			}
		}
	]);
	$.module.validator.add('#passwd', [
		'noempty',
		{ exec:'length',args:[6,20] },
		{
			exec:function(){
				return !(/\s/g.test(this.val()));
			},
			error:'只能包含大小写字母、数字以及标点符号（除空格）'
		},
		{
			exec:function(){
				var _result = 0,
					_value = this.val();
				/\d/g.test(_value) && _result++;
				/[a-z]/g.test(_value) && _result++;
				/[A-Z]/g.test(_value) && _result++;
				/[\W|_]/g.test(_value) && _result++;
				return _result >= 2;
			},
			error:'大写字母、小写字母、数字和标点符号至少包含2种'
		},
		{
			exec:function(){
				return $.module.pstrength.checkPassword(this) > 0;
			},
			error:function(){
				var nPerc = $.module.pstrength.checkPassword(this);
				return $.module.pstrength.getErrormsg(nPerc);
			}
		}
	]);
	$.module.validator.add('#passwd1', [
		'noempty',
		{
			exec:function(){ 
				return $.trim(this.val()) === $.trim($('#passwd').val()); 
			},
			error:'两次输入密码不一致'
		}
	]);
	$.module.validator.add('#captcha', [
		'noempty',			
		{
			exec:'length',
			args:[4,4],
			error:'验证码错了'
		}
	]);

	$('#captcha').on('blur', function(){
		if($(this).hasClass('fm-haserror')){
			return;
		}
		var value = $.trim($(this).val());
		if(value == ''){
			return;
		}
		$.ajax({
			url : "{'system/checkcode'|app}",
			dataType:'json',
			data : {
				'value' : value
			},			
			success : function(data){
				if(!data.success) {
					$.module.validator.error('#captcha', data.msg);
				}
			}
		});
	});

	$('#email').on('blur', function(){
		if($(this).hasClass('fm-haserror')){
			return;
		}
		var value = $.trim($(this).val());
		if(value == ''){
			return;
		}
		$.ajax({
			url : "{'passport/join/validate/mail'|app}",
			dataType:'json',
			data : {
				'value' : value
			},			
			success : function(data){
				if(!data.success) {
					$.module.validator.error('#email', data.msg);
				}
			}
		});
	});

	if($('#invite_code').length){
		if(invite_required){
			$.module.validator.add('#invite_code',['noempty']);
		}
						
		$('#invite_code').on('blur', function(){
			if($(this).hasClass('fm-haserror')){
				return;
			}			
			var value = $.trim($(this).val());
			if(value == '' || value == '0'){
				return;
			}
			$.ajax({
				url : "{'passport/join/validate/code'|app}",
				dataType:'json',
				data : {
					'value' : value
				},			
				success : function(data){
					if(!data.success) {
						$.module.validator.error('#invite_code', data.msg);
					}
				}
			});
		});
	}
};
$(function(){
	$.module.pstrength.create('#passwd');
	_initValidator();
	$('#regist-form').on('submit',function(ev){
		if($(this).find('.fm-haserror').length){
			ev.preventDefault();
			return false;
		}
		if(!$.module.validator.validateAll('#regist-form')){
			ev.preventDefault();
			return false;
		}
		$('#registersubmit').attr('disabled',true).val('正在注册...');
	});
});
</script>
<!-- end join form -->
{include 'passport/views/t1/footer.tpl'}
