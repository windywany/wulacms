<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="applicable-device" content="pc"/>
    <title>登录 - {'site_name'|cfg}</title>
    <link type="text/css" href="{'css/account.css'|here}" rel="stylesheet"/>
    <link type="text/css" href="{'css/common.css'|here}" rel="stylesheet"/>
</head>
<body>
<div class="account-main">
    <div class="account">
        <div class="account-title">登录</div>
        <form id="form_login" method="post">
            <input type="hidden" name="_form_id" value="{$_form_id}"/>
            <input type="hidden" name="salt" value="{$salt}"/>
            <p class="message" id="message"></p>
            <ul>
                <li class="no-margin">
                    <input class="text-input input_300" type="text" name="username" placeholder="手机/邮箱"/>
                </li>
                <li>
                    <input class="text-input input_300" type="password" name="passwd" placeholder="您的密码"/>
                    <p><a href="{'passport/forget'|app}">忘记密码？</a></p>
                </li>
                <li id="captcha_wrapper" {if !$captcha}style="display: none"{/if}>
                    <img src="{'system/captcha/png/100x44/16'|app}" id="captcha">
                    <input name="captcha" class="text-input input_190 captcha" type="text" placeholder="验证码">
                </li>
                <li>
                    <input type="submit" class="account-btn btn-orange" value="立即登录"/>
                    {if $allowJoin}
                        <p><a href="{'passport/join'|app}" class="text-orange">没有账户？点击这里立即注册</a></p>
                    {/if}
                </li>
            </ul>
        </form>
    </div>
</div>
<div class="account-footer">版权所有</div>
<script type="text/javascript">
    var formRules = {$rules};
</script>
<script type="text/javascript" src="{'jquery.js'|assets}"></script>
<script type="text/javascript" src="{'jquery/plugins/jquery-validate/jquery.validate.min.js'|assets}"></script>
<script type="text/javascript" src="{'jquery/plugins/jquery-validate/validate_method.js'|assets}"></script>
<script type="text/javascript" src="{'nui/widgets/validate.js'|assets}"></script>
<script type="text/javascript" src="{'js/login.js'|here}"></script>

</body>
</html>