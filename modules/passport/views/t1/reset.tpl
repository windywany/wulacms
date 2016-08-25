{include 'passport/views/t1/header.tpl'}
{if $step == '1'}
<form class="c_form" method="post" action="{'passport/forgetpassword'|app}" name="loginform" id="loginform" style="margin-bottom:25px;">
	<input type="hidden" name="_form_id" value="{$_form_id}"/>
	<table cellspacing="0" cellpadding="0" class="formtable">
		<caption>
			<h2>重设密码</h2>
			<p>请输入您在本站注册时使用的邮箱地址以便重设您的密码.如果您还不是本站会员您可以<a href="{'passport/join'|app}">立即注册</a>成为本站会员.</p>			
		</caption>
		<tbody>

			<tr>
				<th width="100" class="fm-label"><span class="noempty">*</span><label for="username">电子邮箱</label></th>
				<td>
					<input type="text" tabindex="1" class="fm-text fm-text-286" id="username" name="username"/>	
					<a href="{$join_url}">想起密码啦</a>
				</td>
			</tr>					
			<tr>
				<th style="vertical-align: top;" class="fm-label"><span class="noempty">*</span>验证码</th>
				<td>
					<img id="img_seccode" src="{'system/captcha/png/95x30/14'|app}" align="absmiddle">
					<p>请输入上面的4位字母或数字，看不清可<a href="javascript:updateseccode()">更换一张</a></p>
					<input id="captcha" name="captcha" class="fm-text" tabindex="3" autocomplete="off" type="text"/>
				</td>
			</tr>			
		</tbody>
		<tbody>
			<tr>
				<th width="100">&nbsp;</th>
				<td>		
					<input type="submit" tabindex="5" class="submit" value="立即重设" name="loginsubmit" id="loginsubmit"/>					
				</td>
			</tr>
		</tbody>		
	</table>		
</form>
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
	$('#username').on('blur', function(){
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
				if(data.success) {
					$.module.validator.error('#username', '邮箱不存在.');
				}
			}
		});
	});
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
		$('#loginsubmit').attr('disabled',true).val('正在重设...');
	});
});
</script>
{else}
<div class="c_form">
	<table cellspacing="0" cellpadding="0" class="formtable">
		<caption>
			<h2>密码已重设</h2>	
			<p>新密码已经发到你的邮箱,请注意查收,并及时<a href="{$join_url}">登录</a>修改密码.</p>	
		</caption>
	</table>
</div>
{/if}
{if $enableOAuth}
	{include 'passport/views/t1/oauth.tpl'}
{/if}
{include 'passport/views/t1/footer.tpl'}