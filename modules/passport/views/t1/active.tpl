{include 'passport/views/t1/header.tpl'}
<div class="c_form">
	<table cellspacing="0" cellpadding="0" class="formtable">
		<caption>
			<h2>账户激活</h2>	
			<p>激活邮件已经发送到您的邮箱，请您在{'code_expire@passport'|cfg}小时内完成激活。<br/>如果您未收到激活邮件(有可能在你的垃圾邮箱中),请<a href="{'passport/active'|app}{$uid}/1/">点击此处</a>重新发送激活邮件.</p>	
		</caption>		
	</table>
</div>
{include 'passport/views/t1/footer.tpl'}