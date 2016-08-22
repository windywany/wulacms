<!DOCTYPE html>
<html lang="en-us" class="{$cp_theme}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="renderer" content="webkit">
<title>管理后台 - {'site_name'|cfg} - Powered by KissCMS!({$KISS_VERSION} {$KISS_RELEASE_VER})</title>
<meta content="kisscms" name="description"/>
<meta content="ninGf" name="author"/>
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
{combinate type="css"}
<link href="{'bootstrap/css/bootstrap.min.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
<link href="{'bootstrap/css/font-awesome.min.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
<link href="{'bootstrap/css/smartadmin-production-plugins.min.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
<link href="{'bootstrap/css/smartadmin-production.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
<link href="{'bootstrap/css/smartadmin-skins.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
<link href="{'kindeditor/themes/default/default.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>	
<link href="{'jquery/plugins/ztree/css/style.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>	
<link href="{'nui/nUI.css'|assets}" rel="stylesheet"/>
<link href="{'quicktags.css'|assets}" rel="stylesheet"/>
<link href="{'google/fonts.css'|assets}" rel="stylesheet"/>
{'on_load_dashboard_css_file'|fire}
<link href="{'assets/cp.css'|here}" media="screen" rel="stylesheet" type="text/css"/>
{/combinate}
<style type="text/css">
{'on_load_dashboard_css'|fire}
</style>
<link href="{'favicon.ico'|base}" rel="shortcut icon" type="image/x-icon"/>
<link href="{'favicon.ico'|base}" rel="icon" type="image/x-icon"/>
</head>

<body class="{$cp_theme}{if $menu_on_top} menu-on-top{/if}" data-validateURL="{$validateURL}">

	<header id="header">	
	<div id="logo-group">		
		<span id="logo"><img alt="KissCms" src="{'img/logo.png'|assets}"></span>		
	</div>
	<!-- pulled right: nav area --> 
	<div class="pull-right">			 
		<div class="btn-header pull-right" id="hide-menu">
			<span>
				<a href="javascript:void(0);" title="显示/隐藏导航菜单" data-action="toggleMenu">
					<i class="fa fa-reorder"></i>
				</a>
			</span>
		</div> 
		<ul id="mobile-profile-img" class="header-dropdown-list hidden-xs padding-5">
			<li class="">
				<a href="#" class="dropdown-toggle no-margin userdropdown" data-toggle="dropdown"> 
					<img src="{'avatars/male.png'|assets}" alt="{$passport->getUserName()}" class="online" />  
				</a>
				<ul class="dropdown-menu pull-right">
					{$layoutManager->renderLink('user')}					
				</ul>
			</li>
		</ul>
		<div class="btn-header transparent pull-right">
			<span>
				<a data-action="logoutUser" data-logout-msg="你确定要注销吗?" href="{'dashboard/signout'|app}" title="退出">
					<i class="fa fa-sign-out"></i>
				</a>
			</span>
		</div> 
		<div class="btn-header transparent pull-right">
			<span>
				<a target="_blank" href="{$HomeURL}" title="网站首页">
					<i class="fa fa-fw fa-home"></i>
				</a>
			</span>
		</div>
		{'on_render_navi_btns'|fire}
		{if $layoutManager->hasLinkes('model')}
		<ul class="header-dropdown-list hidden-xs" style="padding-left: 10px;">
			<li>
				<a data-toggle="dropdown" class="dropdown-toggle bg-color-red" href="#" id="fbnews">					
					<i class="fa fa-lg fa-file-text"></i> 
					新建
					<i class="fa fa-angle-down"></i>
				</a>
				<ul class="dropdown-menu multi-level pull-right">
					{$layoutManager->renderLink('model')}
				</ul>
			</li>
		</ul>
		{/if}
		<form onsubmit="return doQuickSearch()" class="header-search pull-right">
			<input id="search-fld" name="param" placeholder="快速查找" type="text" style="min-width: 300px;"/>
			<button type="button" id="search-form-submitter">
				<i class="fa fa-search"></i>
			</button>			
		</form>
		
	</div>
	</header>
	<!-- END HEADER -->
	<aside id="left-panel">
		 <!-- User info -->
		<div class="login-info">
			<span data-toggle="dropdown" class="dropdown-toggle">				
				<a href="javascript:void(0);" id="show-shortcut">
					<img alt="me" class="online" src="{'avatars/male.png'|assets}"/>
					<span>{$passport->getUserName()}</span>
					<i class="fa fa-angle-down"></i>
				</a>				
			</span>
			<ul class="dropdown-menu">
				{$layoutManager->renderLink('user')}				
			</ul>
		</div>
		<!-- end user info -->
		<nav>
			<ul>
				<li>
					<a href="{'dashboard/cp'|app:0}" title="控制面板">
						<i class="fa fa-lg fa-fw fa-home"></i>
						<span class="menu-item-parent">开始</span>
					</a>
				</li>
				{$layoutManager->renderNaviMenu()}				
			</ul>
		</nav>
		<span class="minifyme" data-action="minifyMenu">
			<i class="fa fa-arrow-circle-left hit"></i>
		</span>
	</aside>
	<!-- END ASIDE -->
	<!-- MAIN PANEL -->
	<div id="main" role="main">
		<!-- RIBBON -->
		<div id="ribbon">
			<span class="ribbon-button-alignment">
				<span class="ribbon-button-alignment btn btn-ribbon" data-html="true" data-placement="bottom" data-title="refresh" id="refresh">
					<i class="fa fa-refresh"></i>
				</span>
			</span>			
			<ol class="breadcrumb"></ol>			
		</div>
		<div id="content"></div>
		<div class="layoutp demo">
			<span id="layoutp-setting">
				<i class="fa fa-cog txt-color-blueDark"></i>
			</span> 
			<form>
				<legend class="no-padding margin-bottom-10">布局设置</legend>
				<section>
					<label>
						<input name="subscription" id="smart-fixed-nav" type="checkbox" class="checkbox style-0"/>
						<span>固定头部</span>
					</label>
					<label>
						<input type="checkbox" name="terms" id="smart-fixed-ribbon" class="checkbox style-0">
						<span>固定导航条</span>
					</label>
					<label>
						<input type="checkbox" name="terms" id="smart-fixed-navigation" class="checkbox style-0">
						<span>固定导航菜单</span>
					</label>
					<label><input type="checkbox" class="checkbox style-0" id="smart-topmenu"><span>菜单<b>置顶</b></span></label>
				</section>										
				<h6 class="margin-top-10 semi-bold margin-bottom-5">皮肤</h6>
				<section id="smart-styles">
					<a href="javascript:void(0);" id="smart-style-0" 
						data-skinlogo="{'img/logo.png'|assets}" class="btn btn-block btn-xs txt-color-white margin-right-5" style="background-color:#4E463F;">
						<i class="fa fa-check fa-fw" id="skin-checked"></i>默认</a>
					<a href="javascript:void(0);" id="smart-style-1" 
						data-skinlogo="{'img/logo-white.png'|assets}" class="btn btn-block btn-xs txt-color-white" 
						style="background:#3A4558;">Dark Elegance</a>
					<a href="javascript:void(0);" id="smart-style-2" 
						data-skinlogo="{'img/logo-blue.png'|assets}" class="btn btn-xs btn-block txt-color-darken margin-top-5" style="background:#fff;">Ultra Light</a>
					<a href="javascript:void(0);" id="smart-style-3" 
						data-skinlogo="{'img/logo-pale.png'|assets}" class="btn btn-xs btn-block txt-color-white margin-top-5" style="background:#f78c40">Google Skin</a>
					<a href="javascript:void(0);" id="smart-style-4" 
					data-skinlogo="{'img/logo-pale.png'|assets}" class="btn btn-xs btn-block txt-color-white margin-top-5" style="background:#bbc0cf">PixelSmash</a>
					<a href="javascript:void(0);" id="smart-style-5" 
						data-skinlogo="{'img/logo-pale.png'|assets}" class="btn btn-xs btn-block txt-color-white margin-top-5" style="background:rgb(153,179,204,0.2);border:1px solid rgb(121,161,221,0.8);color:#172730 !important">Glass</a>
				</section>
				<br/>
				<section class="margin-top-10">						
					<span id="reset-smart-widget-style" 
						class="btn btn-xs btn-block btn-primary"
						data-reset-msg="你确定要重置组件样式吗?">
						<i class="fa fa-refresh"></i> 重置小部件
					</span>
				</section>
			</form>
		</div>
	</div>
	<div class="page-footer">
		<div class="row">
			<div class="col-xs-12 col-sm-6"><span class="txt-color-white">KissCms {$KISS_VERSION} {$KISS_RELEASE_VER} <span class="hidden-xs"> - Powered by <a href="#">KissGO!</a> </span> &copy; 2014 - {'Y'|date}</span></div>
		</div>
	</div>
	<!-- END MAIN PANEL -->	
	{combinate type="js"}	
	<script type="text/javascript" src="{'jquery/jquery-2.1.1.min.js'|assets}"></script>	
	<script type="text/javascript" src="{'jquery/jquery-ui-1.10.4.min.js'|assets}"></script>
	<script type="text/javascript" src="{'quicktags.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/jquery-touch/jquery.ui.touch-punch.min.js'|assets}"></script> 
	<script type="text/javascript" src="{'bootstrap/bootstrap.min.js'|assets}"></script>
	<script type="text/javascript" src="{'bootstrap/SmartNotification.js'|assets}"></script>
	<script type="text/javascript" src="{'bootstrap/jarvis.widget.min.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/easy-pie-chart/jquery.easy-pie-chart.min.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/sparkline/jquery.sparkline.min.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/jquery-validate/jquery.validate.min.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/jquery-validate/validate_method.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js'|assets}"></script>	
	<script type="text/javascript" src="{'jquery/plugins/select2/select2.min.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/jquery-nestable/jquery.nestable.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/bootstrap-slider/bootstrap-slider.min.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/x-editable/x-editable.min.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/msie-fix/jquery.mb.browser.min.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/fastclick/fastclick.min.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/jquery-superbox/jquery.superbox-min.js'|assets}"></script>	
	<script type="text/javascript" src="{'jquery/plugins/flot/jquery.flot.cust.min.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/flot/jquery.flot.resize.min.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/flot/jquery.flot.tooltip.min.js'|assets}"></script>
	<script type="text/javascript" src="{'kindeditor/kindeditor-all.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/plupload.full.min.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/ztree/jquery.ztree.core.min.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/plugins/ztree/jquery.ztree.excheck.min.js'|assets}"></script>
	<script type="text/javascript" src="{'nui/nUI.js'|assets}"></script>
	<script type="text/javascript" src="{'nui/widgets/dialog.js'|assets}"></script>
	<script type="text/javascript" src="{'nui/widgets/ajax.js'|assets}"></script>
	<script type="text/javascript" src="{'nui/widgets/table.js'|assets}"></script>
	<script type="text/javascript" src="{'nui/widgets/grid.js'|assets}"></script>
	<script type="text/javascript" src="{'nui/widgets/pager.js'|assets}"></script>
	<script type="text/javascript" src="{'nui/widgets/combox.js'|assets}"></script>
	<script type="text/javascript" src="{'nui/widgets/searchform.js'|assets}"></script>
	<script type="text/javascript" src="{'nui/widgets/validate.js'|assets}"></script>
	<script type="text/javascript" src="{'nui/widgets/ajaxupload.js'|assets}"></script>
	<script type="text/javascript" src="{'nui/widgets/kindeditor.js'|assets}"></script>
	<script type="text/javascript" src="{'nui/widgets/tagwrapper.js'|assets}"></script>
	<script type="text/javascript" src="{'nui/widgets/treeview.js'|assets}"></script>
	{'on_load_dashboard_js_file'|fire}
	<script type="text/javascript" src="{'assets/cp.js'|here}"></script>
	{/combinate}
	{'on_load_dashboard_js'|fire}
	<script type="text/javascript">	
		window.KissCms = { 'AppURL':'{$AppURL}','SiteURL':'{$SiteURL}' };
	    $.sound_path = "{'sound'|assets}/";
	    window.KissCms.assetsURL = "{''|assets}";
	    $.siteName = '{$SiteName} - Powered by KissCMS!({$KISS_VERSION} {$KISS_RELEASE_VER})';
	    $(function(){
	    	KindEditor.options.basePath = "{'kindeditor'|assets}/";
	    	nUI.init({ debug:false });
		    if ($('nav').length) {		    	
			    checkURL();
		    }
		    $('#{$cp_theme}').click();
		    {'on_dashboard_window_ready_scripts'|fire}
	    });
	    function doQuickSearch(){
	    	$('#search-form-submitter').click();
			return false;
		}
	</script>
</body>
</html>