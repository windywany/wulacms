<!DOCTYPE html>
<html lang="en-us">
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta content="kisscms" name="description" />
<meta content="ninGf" name="author" />
<meta
	content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"
	name="viewport" />
<title>火车头登录</title>
<link href="{'favicon.ico'|base}" rel="shortcut icon"
	type="image/x-icon" />
<link href="{'favicon.ico'|base}" rel="icon" type="image/x-icon" />
</head>
<body>
	<form name="LoginForm" action="{'locoy'|app}" method="POST">		
		<fieldset>
			{if $errorMsg}
			<p>LOGIN-ERROR:{$errorMsg}</p>			
			{/if}			
			<section>
				<label class="label">用户名(或邮箱)</label> <label class="input">
					<input tabindex="1" type="text" name="username" id="username"
					value="{$username}" />
				</label>
			</section>
			<section>
				<label class="label">密码</label> <label class="input"><input
					tabindex="2" type="password" name="passwd" id="passwd" /></label>
			</section>
			<section>
				<label class="label">安全码</label> <label class="input"><input
					tabindex="3" type="text" name="safe_code" id="safe_code" /></label>
			</section>
		</fieldset>
		<footer>
			<button type="submit" tabindex="4" class="btn btn-primary">
				登录</button>
		</footer>
	</form>
</body>
</html>
