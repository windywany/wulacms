<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="applicable-device" content="pc"/>
    <title>登录 - {'site_name'|cfg}</title>
    <link type="text/css" href="{'css/account.css'|here}" rel="stylesheet"/>
    <link type="text/css" href="{'css/common.css'|here}" rel="stylesheet"/>
</head>
<style>
.top,.dingbu{ width:905px; overflow:hidden; margin:auto;}
.logo{ width:268px; height:68px; float:left;}
.dh{ float:left; overflow:hidden; margin-left:20px; width:615px; height:68px; line-height:68px;}
.dh div{ float:left; font-size:18px; width:200px;}
.dh a{ display:block; float:right; font-size:20px;}
</style>
<body>
<div class="account-main">
<div class="dingbu">
<div class="top">
<div class="logo"><img src="http://jishi.ichong123.com/static/images/logo.png"></div>
<div class="dh">
<div>欢迎来到爱宠会员中心!</div>
<a href="http://jishi.ichong123.com/">返回首页</a>
</div>
</div>
</div>
<div class="denglu">
<div class="dl_left"><img src="http://my.ichong123.com/files/2016/11/88/zhuce.jpg" width="500" height="310" alt=""/></div>
    <div class="account">
        <div class="account-title">登录</div>
        <form id="form_login" method="post">
            <input type="hidden" name="_form_id" value="{$_form_id}"/>
            <input type="hidden" name="salt" value="{$salt}"/>
            <ul>

                <li class="no-margin">
                    <input class="text-input input_300" type="text" name="username" placeholder="手机号"/>
                </li>
                <li>
                    <input class="text-input input_300" type="password" name="passwd" placeholder="您的密码"/>
                    <p><a href="{'passport/forget'|app}">忘记密码？</a></p>
                </li>
                <li id="captcha_wrapper" {if !$captcha}style="display: none"{/if}>
                    <img src="{'system/captcha/png/100x44/16'|app}" id="captcha">
                    <input name="captcha" class="text-input input_190 captcha" type="text" placeholder="验证码">
                </li>
                <li id="message-wraper" style="display: none">
                    <p class="text-red" id="message"></p>
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
    <div class="account-footer">版权所有：爱宠网（www.ichong123.com） <a href="http://www.ichong123.com/about.html">爱宠介绍</a> - <a href="http://www.ichong123.com/about.html#ggfw">推广服务</a> - <a href="http://www.ichong123.com/about.html#lxwm">联系我们</a> - <a href="http://www.ichong123.com/about.html#mzsm">免责声明</a></div>
</div>
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