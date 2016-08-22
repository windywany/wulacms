<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>  
  <meta charset="utf-8"> 
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
  <title>登录 - {'site_name'|cfg} - Powered by KissCMS! - {$KISS_VERSION} {$KISS_RELEASE_VER}</title>
  <link href="{'bootstrap/css/bootstrap.min.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
  <link href="{'bootstrap/css/font-awesome.min.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
  <link rel="stylesheet" href="{'assets/login.css'|here}">
  <link href="{'favicon.ico'|base}" rel="shortcut icon" type="image/x-icon"/>
  <link href="{'favicon.ico'|base}" rel="icon" type="image/x-icon"/>
  <!--[if lt IE 9]>
  <script src="{'html5shiv.js'|assets}"></script>
  <script src="{'respond.min.js'|assets}"></script>
  <![endif]-->
</head>

<body class="account-bg">
<hr class="account-header-divider">
<div class="account-wrapper">
  <div class="account-logo">
    <a href="{$HomeURL}"><img src="{'assets/logo.png'|here}" alt="KissCMS!"/></a>
  </div>
    <div class="account-body">
      <h3 class="account-body-title">欢迎使用KissCMS!建站.</h3>
	  {if $errorMsg}
		<div class="alert alert-danger" style="margin:0 25px;">
			<button data-dismiss="alert" class="close">×</button>			
			{$errorMsg}
		</div>
		{else}
      <h5 class="account-body-subtitle">请使用您的账户登录</h5>
		{/if}
      <form class="form account-form" method="POST" action="{'dashboard'|app}">
        <div class="form-group">
          <label for="username" class="placeholder-hidden">用户名</label>
          <input class="form-control" tabindex="1" placeholder="用户名(或邮箱)" type="text" name="username" id="username" value="{$username}">
		</div>

        <div class="form-group">
          <label for="login-password" class="placeholder-hidden">密码</label>
          <input type="password" class="form-control" placeholder="密码" name="passwd" id="passwd" tabindex="2">
        </div>
		{if $captcha}
        <div class="form-group clearfix">
          <div class="pull-left">         
            <label for="captcha" class="placeholder-hidden">验证码</label>
            <input class="form-control" tabindex="3" placeholder="验证码" type="text" name="captcha" id="captcha">
          </div>
          <div class="pull-right">
            <img id="captcha-img" src="{'system/captcha/png/95x30/14'|app}"/>
          </div>
        </div> 
		{/if}
        <div class="form-group">
          <button type="submit" class="btn btn-primary btn-block btn-lg" tabindex="4">
          	 登录 &nbsp; <i class="fa fa-play-circle"></i>
          </button>
        </div>
      </form>

    </div>
    <div class="account-footer">
      <p>
     	 本站点由 <a href="http://www.crudq.com/" target="_blank">KissCMS!</a> - {$KISS_VERSION} {$KISS_RELEASE_VER} 驱动.
      </p>
    </div> 
  </div>
  <script type="text/javascript" src="{'jquery/jquery-2.1.1.min.js'|assets}"></script>	
  <script type="text/javascript" src="{'bootstrap/bootstrap.min.js'|assets}"></script>
  <script type="text/javascript">
	    $(function(){	    	
		    if($('#captcha-img').length>0){		    	
			    var imgSrc = $('#captcha-img').attr('src');
				$('#captcha-img').click(function(){ 
					$(this).attr('src',imgSrc+'&_t='+(new Date().getTime()));
				});
			}			
	    });
	</script>  
</body>
</html>
