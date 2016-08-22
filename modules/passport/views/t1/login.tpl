{include 'passport/views/t1/header.tpl'}
<form class="c_form" method="post" action="{'passport'|app}" name="loginform" id="loginform" style="margin-bottom:25px;">
	<input type="hidden" name="_form_id" value="{$_form_id}"/>
	<table cellspacing="0" cellpadding="0" class="formtable">
		<caption>
			<h2>登录</h2>
			<p>如果您已经是本站会员请在这儿登录.</p>
			{if $errorMsg}
			<div style="color:red; font-weight:bold;">{$errorMsg}</div>
			{/if}
		</caption>
		<tbody>

			<tr>
				<th width="100" class="fm-label"><span class="noempty">*</span><label for="username">电子邮箱</label></th>
				<td>
					<input type="text" tabindex="1" value="{$username}" class="fm-text fm-text-286" id="username" name="username"/>
					{if $allowJoin}
					<a href="{'passport/join'|app}">立即注册!</a>
					{/if}
				</td>
			</tr>
			<tr>
				<th width="100" class="fm-label"><span class="noempty">*</span><label for="password">密&#12288;码</label></th>
				<td>
					<input type="password" value="" tabindex="2" class="fm-text fm-text-286" id="password" name="passwd"/>
					{if $passport_type =='vip'}
					<a href="{'passport/forgetpassword'|app}">忘记密码?</a>
					{/if}
				</td>
			</tr>
			{if $captcha}
			<tr>
				<th style="vertical-align: top;" class="fm-label"><span class="noempty">*</span>验证码</th>
				<td>
					<img id="img_seccode" src="{'system/captcha/png/95x30/14'|app}" align="absmiddle">
					<p>请输入上面的4位字母或数字，看不清可<a href="javascript:updateseccode()">更换一张</a></p>
					<input id="captcha" name="captcha" class="fm-text" tabindex="3" autocomplete="off" type="text"/>
				</td>
			</tr>
			{/if}
		</tbody>
		<tbody>
			<tr>
				<th width="100">&nbsp;</th>
				<td>		
					<input type="submit" tabindex="5" class="submit" value="登录" name="loginsubmit" id="loginsubmit"/>					
				</td>
			</tr>
		</tbody>		
	</table>		
</form>
{if $enableOAuth}
	{include 'passport/views/t1/oauth.tpl'}
{/if}
<script type="text/javascript">
var imgSrc = $('#img_seccode').attr('src');
function updateseccode(){
	$('#img_seccode').attr('src',imgSrc+'&_t='+(new Date().getTime()));	
}
$(function(){
	
	$.module.validator.add('#username', [
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

	$.module.validator.add('#password', [
		'noempty',
		{ exec:'length',args:[6,20] }
	]);

	if($('#captcha').length){
		$.module.validator.add('#captcha', [
			'noempty',			
			{
				exec:'length',
				args:[4,4],
				error:'Erro Code'
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
	}
	
	$('#loginform').on('submit',function(ev){
		if($(this).find('.fm-haserror').length){
			ev.preventDefault();
			return false;
		}
		if(!$.module.validator.validateAll('#loginform')){
			ev.preventDefault();
			return false;
		}
		$('#loginsubmit').attr('disabled',true).val('登录中 ...');
	});
});
</script>
{include 'passport/views/t1/footer.tpl'}