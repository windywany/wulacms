<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html style="background:#fff;">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>审核</title>
{combinate type="css"}
<link href="{'bootstrap/css/bootstrap.min.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
<link href="{'bootstrap/css/font-awesome.min.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
<link href="{'bootstrap/css/smartadmin-production.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
<link href="{'bootstrap/css/smartadmin-skins.css'|assets}" media="screen" rel="stylesheet" type="text/css"/>
<link href="{'nui/nUI.css'|assets}" rel="stylesheet"/>
<link href="{'google/fonts.css'|assets}" rel="stylesheet"/>
{/combinate}
<script type="text/javascript">
	function showIframe(){
		$('#loadding-tip').hide();
		$('#previewFrame').show();
		$('#approving-btns').show();
	}
</script>
</head>
<body style="background:#fff;">
	{if $id}
		<div style="padding:25px">
			<div id="loadding-tip" style="text-align:center;">正在加载,请稍候......</div>
		</div>
		<iframe src="{$url}?preview" style="border:0;position:absolute;top:0;left:0;display:none" id="previewFrame" onload="showIframe()"></iframe>
		<div class="page-footer" style="padding:15px 13px 0 20px;">
			<div class="row">
				<div class="col-xs-12 col-sm-4 hidden-xs">
					<span class="txt-color-white">{$title}{if $title2}({$title2}){/if}</span>
				</div>
				<div class="col-xs-12 col-sm-4">
					<div id="approving-btns" class="btn-group" style="margin-top:-8px;display:none;">
						<a class="btn btn-danger" href="{'cms/approve/approve/0'|app}?ids={$id}">拒绝</a>
						<a class="btn btn-success" href="{'cms/approve/approve/1'|app}?ids={$id}">通过</a>
						{if $publish_time}
						<a class="btn btn-primary" href="{'cms/approve/approve/2'|app}?ids={$id}">定于{$publish_time|date_format:'Y-m-d H:i'}发布</a>
						{/if}
					</div>					
				</div>
				<div class="col-xs-6 col-sm-4 text-right hidden-xs">					
					<div class="txt-color-white inline-block">
						<i class="txt-color-blueLight hidden-mobile">由{$uuname}更新于 <i class="fa fa-clock-o"></i> <strong>{$update_time|date_format:'Y-m-d H:i'} &nbsp;</strong> </i>
					</div>
					<!-- end div-->
				</div>
			</div>
		</div>	
	{else}
	<div style="padding:25px">
		<div class="alert alert-success fade in">				
			<i class="fa-fw fa fa-check"></i>
			<strong>恭喜！</strong> 已经全部审核完毕.<a href="javascript:window.close();">关闭本页面.</a>
		</div>
	</div>
	{/if}
	{combinate type="js"}	
	<script type="text/javascript" src="{'jquery/jquery-2.1.1.min.js'|assets}"></script>	
	<script type="text/javascript" src="{'bootstrap/bootstrap.min.js'|assets}"></script> 
	{/combinate}	
	<script type="text/javascript">
		$(function(){
			$(window).resize(function(){
				var w = $(window).width(),h=$(window).height();				
				$('#previewFrame').css({
					width:w,
					height:h-52
				})
			}).resize();
	    });
	</script>
</body>
</html>
