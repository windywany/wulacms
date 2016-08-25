<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
		<h1 class="page-title txt-color-blueDark">
			<i class="fa-fw fa fa-home"></i> 控制面板			
		</h1>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">
		{'dashboard_right_bar'|fire}
		<ul id="sparks">{'dashboard_sparks_bar'|fire}</ul>	
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		{if !$noticeClosed}
		<div class="alert alert-warning fade in">
			<button data-dismiss="alert" class="close" 
					data-blockUI="false" 
					data-url="{'dashboard/closenotice'|app}" 
					target="ajax">×</button>
			<p><i class="fa-fw fa fa-info"></i><strong>{$noticeTitle}:</strong></p>
			<div>{$noticeMessage}</div>	
			<div class="text-right">{$noticeUser} - {$noticeTime}</div>	
		</div>		
		{/if}
		<div id="shortcut-wrap">			
			<ul>{'on_render_dashboard_shortcut'|fire}</ul>			
		</div>
	</div>
</div>
{$dashboardUI|render}
{'render_dashboard_panel'|fire}
<div class="row">
	<div class="col-sm-12">
		<div class="alert alert-success fade in">
			<i class="fa-fw fa fa-info"></i>
			<strong>运行时信息:</strong><br/>
			<p> 开发模式: {$devMod}</p>
			<p> 日志级别: {$logLevel}</p>
			<p> HTTP Server: {$serverName}</p>
			<p> PHP: {$phpInfo}</p>
			<p> 数据库: {$dbInfo}</p>
			<p> GD: {$gdInfo}</p>
			<p> 会话管理器: {$sessionManager}</p>			
			<p> 分词系统: {$scwsInfo}</p>
			<p> 运行时缓存: {$rtcacheInfo}</p>
			{'show_system_info'|fire}		
		</div>	
		<div class="alert alert-info fade in">
			<i class="fa-fw fa fa-info"></i>
			<strong>程序信息:</strong><br/>
			<p> 设计开发: 喜羊羊 QQ:2022196399, 邮箱: <a href="mailto:windywany@163.com">windywany@163.com</a></p>
			<p> 功能需求: 猴哥,喜羊羊</p>
			<p> 程序版本: {$KISS_VERSION} {$KISS_RELEASE_VER}, BUILD:{$KISS_BUILD_ID}, 内核版本:{$kernelVer}, 界面版本:{$cpVer}</p>
		</div>
	</div>
</div>
