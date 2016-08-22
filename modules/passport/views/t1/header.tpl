<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta http-equiv="x-ua-compatible" content="ie=7">
		<title>通行证 - {$SiteName}</title>		
		<link href="{'default/style.css'|here}" media="screen" rel="stylesheet" type="text/css"/>
		{if cfg('style@passport') == 'blue'}
		<link href="{'blue/style.css'|here}" media="screen" rel="stylesheet" type="text/css"/>
		{elseif cfg('style@passport') == 'green'}
		<link href="{'green/style.css'|here}" media="screen" rel="stylesheet" type="text/css"/>
		{/if}
		<link href="{'comm.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
		<script type="text/javascript" src="{'jquery.js'|assets}"></script>
		<script type="text/javascript" src="{'comm.js'|assets}"></script>
		<link href="{'favicon.ico'|base}" rel="shortcut icon" type="image/x-icon"/>
		<link href="{'favicon.ico'|base}" rel="icon" type="image/x-icon"/>
	</head>	
	<body>
		<div id="header">
			<div class="headerwarp">
				<h1 class="logo"><a href="{'passport'|app}"><img src="{'s.gif'|assets}" alt="通行证"></a></h1>
				<ul class="menu">
					<li><a href="{'passport'|app}">首页</a></li>					
				</ul>
			</div>
		</div>

		<div id="wrap"><!-- wrap -->