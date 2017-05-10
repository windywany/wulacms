<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>恭喜您, 注册成功!</title>
    {combinate type="css"}
        <link href="{'bootstrap/css/bootstrap.min.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
        <link href="{'bootstrap/css/font-awesome.min.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
        <link href="{'bootstrap/css/smartadmin-production.css'|assets}" media="screen" rel="stylesheet"
              type="text/css"/>
        <link href="{'bootstrap/css/smartadmin-skins.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
        <link href="{'kindeditor/themes/default/default.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
        <link href="{'nui/nUI.css'|assets}" rel="stylesheet"/>
        <link href="{'google/fonts.css'|assets}" rel="stylesheet"/>
    {/combinate}
</head>
<body>
<h1 class="text-align-center"> 恭喜您,你在{'site_name'|cfg}的账户已经注册成功,5秒后自动跳转到<a href="{$login_url}">登录</a>页</h1>
<script type="text/javascript">
    setTimeout(function () {
        window.location.href = "{$login_url}";
    }, 15000);
</script>
</body>
</html>