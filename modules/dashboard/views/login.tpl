<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <title>亲~，欢迎回来！ - {'site_name'|cfg} - Powered by wulacms! - {$KISS_VERSION} {$KISS_RELEASE_VER}</title>
    {combinate type="css"}
    <link href="{'bootstrap/css/bootstrap.min.css'|assets}" media="screen" rel="stylesheet" type="text/css" />
    <link href="{'bootstrap/css/smartadmin-production.css'|assets}" media="screen" rel="stylesheet" type="text/css" />
    <link href="{'bootstrap/css/font-awesome.min.css'|assets}" media="screen" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{'assets/login.css'|here}">
    {/combinate}
    <link href="{'favicon.ico'|base}" rel="shortcut icon" type="image/x-icon" />
    <link href="{'favicon.ico'|base}" rel="icon" type="image/x-icon" />
</head>

<body>
    <div class="banner containter">
        <h1 class="logo ellipsis">WulaCMS</h1>
    </div>
    <!-- 输入密码开始 -->
    <div class="main">
        <div class="container">
            <form class="sign-in form-inline" method="POST" action="{'dashboard'|app}">
            	{if $errorMsg}
                <div class="alert alert-danger fade in">
					 {$errorMsg}
				</div>
                {else}
                <p class="form-title ellipsis">亲~, 欢迎您回来^_^</p>
                {/if}
                {if $captcha}
                <div class="row code-row">
                    <div class="form-group col-md-5 col-sm-5 col-xs-6 ">
                        <div class="input-group username ">
                            <input class="form-control" tabindex="1" placeholder="验证码" type="text" name="captcha" id="captcha">
                        </div>
                    </div>
                    <div class="form-group col-md-7 col-sm-7 col-xs-6">
                        <div class="input-group code-group" id="code-btn">
                            <img id="code-img" src="{'system/captcha/png/100x34/14'|app}" />
                            <span id="cimg">换一张</span>
                        </div>
                    </div>
                </div>
                {/if}
                <div class="row">
                    <div class="form-group col-md-5 col-sm-5 col-xs-12 ">
                        <label for="username" class="sr-only">username</label>
                        <div class="input-group username ">
                            <input class="form-control" tabindex="2" placeholder="用户名(或邮箱)" type="text" name="username" id="username" value="{$username}" />
                        </div>
                    </div>
                    <div class="form-group col-md-5 col-sm-5 col-xs-12">
                        <label for="passwd" class="sr-only">password</label>
                        <div class="input-group password">
                            <input type="password" class="form-control" placeholder="密码" name="passwd" id="passwd" tabindex="3">
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-2 col-sm-2 log-btn">
                        <button type="submit" class="btn btn-primary" id="log" tabindex="4">登录</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 clearfix for-btn">
                    	<a href="#" class="btn btn-link reset-password"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span>忘记密码？</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- 输入密码结束 -->
    {combinate type="js"}
    <script type="text/javascript" src="{'jquery/jquery-2.1.1.min.js'|assets}"></script>
    <script type="text/javascript" src="{'bootstrap/bootstrap.min.js'|assets}"></script>
    {/combinate}
    <script type="text/javascript">
    {minify type="js"}
        $(function() {
            if ($('#code-img').length > 0) {
                var imgSrc = $('#code-img').attr('src');
                $('#code-img').click(function() {
                    $(this).attr('src', imgSrc + '?_t=' + (new Date().getTime()));
                });
                $('#cimg').click(function() {
                    $('#code-img').attr('src', imgSrc + '?_t=' + (new Date().getTime()));
                });
            }
        });
    {/minify}
    </script>
</body>

</html>