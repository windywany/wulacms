<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="applicable-device" content="pc"/>
    <title>注册-钓鱼之家 - 钓鱼之家</title>
    <link type="text/css" href="{'css/account.css'|here}" rel="stylesheet"/>
    <link type="text/css" href="{'css/common.css'|here}" rel="stylesheet"/>
</head>
<body>
<div class="account-main">
    <div class="account">
        <div class="account-title">手机注册</div>
        <form id="form_register" method="post">
            <input type="hidden" name="type" value="phone"/>
            <input type="hidden" name="_form_id" value="{$_form_id}"/>
            <p class="message" id="message"></p>
            <ul>
                <li class="no-margin">
                    <input name="phone" id="phone" class="text-input input_300" type="text" placeholder="手机号"/>
                </li>
                <li>
                    <input name="passwd" class="text-input input_300" type="password" placeholder="密码"/>
                </li>
                <li>
                    <input name="nickname" class="text-input input_300" type="text" placeholder="昵称"/>
                </li>
                {if $enableInvation}
                    <li>
                        <input name="invite_code" class="text-input input_300" type="text" value="{$rc}"
                               placeholder="邀请码"/>
                    </li>
                {/if}
                <li id="captcha_wrapper" {if !$captcha}style="display: none"{/if}>
                    <img src="{'system/captcha/png/100x44/16'|app}" id="captcha">
                    <input name="captcha" class="text-input input_190 captcha" type="text" placeholder="验证码">
                </li>
                <li>
                    <a class="account-captcha" id="vcode" href="javascript:;">免费获取验证码</a>
                    <input name="vcode" class="text-input input_140" type="text"/>
                </li>
                <li>
                    <input type="hidden" name="type" value="mobile"/>
                    <input type="submit" class="account-btn" value="立即注册"/>
                    <p><a href="{'passport'|app}" class="text-orange">已经有账户？点这里立即登录</a></p>
                </li>
            </ul>
        </form>
    </div>

</div>
<div class="account-footer">版权所有</div>
<script type="text/javascript">
    var sendSmsUrl = "{'sms/send'|app}";
    var formRules  = {$rules};
</script>
<script type="text/javascript" src="{'jquery.js'|assets}"></script>
<script type="text/javascript" src="{'jquery/plugins/jquery-validate/jquery.validate.min.js'|assets}"></script>
<script type="text/javascript" src="{'jquery/plugins/jquery-validate/validate_method.js'|assets}"></script>
<script type="text/javascript" src="{'nui/widgets/validate.js'|assets}"></script>
<script type="text/javascript" src="{'js/join_phone.js'|here}"></script>
</body>
</html>