<?php
define ( 'WEB_ROOT', dirname(dirname ( __FILE__ )) . DIRECTORY_SEPARATOR );
$_kissgo_processing_installation = true;
include_once WEB_ROOT . 'bootstrap.php';
include_once dirname ( __FILE__ ).DS.'assets.php';
$step = rqst('step','welcome');
if(file_exists(APPDATA_PATH.'settings.php') && file_exists(APPDATA_PATH.'install.lock')){
	Response::redirect(BASE_URL);
}
$lock_file = getenv('TMP').DS.'.kissgo.lock';
if(file_exists($lock_file)){		
	if(!sess_get('installing',false)){
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>		
	    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	    <meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>wulacms安装向导</title>
	</head>
	<body><p>另一个安装进程正在进行，请稍后 ...</p><p>如果你是网站管理员，你可以删除<?php echo $lock_file?>文件，然后重新开始安装.</p></body>
</html>
<?php
		exit();
	}	
}else{
	set_time_limit(0);
	$time = time();
	@$_SESSION['installing'] = @file_put_contents($lock_file, $time);
}
if($step == 'welcome'){
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>		
	    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	    <meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>wulacms安装向导</title>
		<link href="../favicon.ico" rel="shortcut icon" type="image/x-icon"/>
		<link href="../favicon.ico" rel="icon" type="image/x-icon"/>
		<link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css"/>
		<link rel="stylesheet" href="../assets/bootstrap/css/bootstrap-theme.min.css"/>
		<link rel="stylesheet" href="../assets/nui/nUI.css"/>
		<link rel="stylesheet" href="install.css"/>
		<!--[if IE]>
		<link href="../assets/nui/nUI4ie.css" rel="stylesheet" type="text/css" media="screen"/>
		<![endif]-->
		<!--[if lte IE 9]>
		<script type="text/javascript" src="../assets/html5shiv.js"></script>
		<script type="text/javascript" src="../assets/respond.min.js"></script>
		<![endif]-->
		<script type="text/javascript" src="../assets/jquery/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="install.js"></script>
		<script type="text/javascript" src="../assets/nui/nUI.js"></script>
		<script type="text/javascript" src="../assets/nui/widgets/dialog.js"></script>
	</head>
	<body role="document" data-siteurl="<?php echo BASE_URL ?>" data-code="<?php echo $time?>">
		<div class="jumbotron subhead">
		  <div class="container">
		    <h1>wulacms安装向导</h1>
		    <p class="lead">欢迎使用开源的wulacms做为您的建站工具和二次开发平台,更多信息请访问<a href="http://www.wulacms.com/">wulacms</a>官方网站.</p>
		    <p class="lead">本向导将引导您完成wulacms的安装.</p>
		  </div>
		</div>
		<div class="container">
			<ul class="nav nav-tabs">
			  <li class="active"><a href="#body" rel="env"><span class="badge">1</span> 环境检测</a></li>
			  <li><a href="#body" rel="setup"><span class="badge">2</span> 配置</a></li>
			  <li><a href="#body" rel="database"><span class="badge">3</span> 数据库</a></li>
			  <li><a href="#body" rel="overview"><span class="badge">4</span> 总览</a></li>
			  <li><a href="#body" id="install"><span class="badge">5</span> 安装</a></li>
			  <li><a href="#body" id="done"><span class="badge">6</span> 完成</a></li>
			</ul>
		</div>
		<div class="container m15" id="body">
			<p class="txtc">正在加载安装页面...</p>
		</div>
		<script type="text/javascript">
	    	$(function(){
            	nUI.init();	
            	KissgoInstaller.init(nUI);	            	
	        });
		</script>
	</body>
</html>
<?php } else if($step == 'env'){
	$envs['safe_mode'] = array('text'=>'安全模式','r'=>0);
	$envs['file_uploads'] = array('text'=>'文件上传','r'=>1);
	$envs['output_buffering'] = array('text'=>'输出缓冲（Output Buffering）','r'=>0);
	$envs['session.auto_start'] = array('text'=>'自动开启Session','r'=>0);	
	$passCheck = true;	
	$apps = KissGoSetting::getBuiltInApps();
	$files = array();
	$exts = array();
	KissgoInstaller::checkFilePermission($files);
	KissgoInstaller::checkPhpExtention($exts);
	if($apps){
		foreach ($apps as $app){
			$installer = AppInstaller::getAppInstaller($app);
			if($installer){
				$installer->checkFilePermission($files);
				$installer->checkPhpExtention($exts);
			}
		}
	}
	?>
<div class="panel panel-default">
  <div class="panel-heading">推荐PHP配置</div>
  <div class="panel-body">
         为了保证wulacms可以良好地运行，推荐如下PHP设置。不过，即使您的设置和推荐的并不完全一样，wulacms 也可以正常工作。
  </div>
  <table class="table table-striped">
  	<thead>
  		<tr>
  			<th>配置项</th>
  			<th width="100" class="txtc">推荐设置</th>
  			<th width="100" class="txtc">实际设置</th>
  		</tr>
  	</thead>
  	<tbody>
  		<?php 
  			foreach($envs as $key=>$value):
  				$rel = strtolower(ini_get($key));
  			    $rel = ($rel == '0' || $rel == 'off' || $rel=='')?0:1;
  			    $clz = $rel == $value['r']?'success':'warning';
  		?>  		
  		<tr>
  			<td><?php echo $value['text']?> </td>
  			<td class="txtc"><span class="label label-success"><?php echo $value['r']?'开':'关'?></span></td>
  			<td class="txtc"><span class="label label-<?php echo $clz?>"><?php echo $rel ? '开':'关'?></span></td>
  		</tr>
  		<?php endforeach;?>  		
  	</tbody>
  </table>
</div>

<div class="panel panel-default">
  <div class="panel-heading">目录读写检测</div>  
  <table class="table table-striped">
  	<thead>
  		<tr>
  			<th>目录</th>
  			<th width="100" class="txtc">要求权限</th>
  			<th width="100" class="txtc">当前权限</th>
  		</tr>
  	</thead>
  	<tbody>
  		<?php 
  			foreach($files as $file=>$value):
  				$clz = $value['pass']?'success':'danger';
  				$passCheck &= $value['pass'];
  		?>  		
  		<tr>
  			<td><?php echo $file?> </td>
  			<td class="txtc"><span class="label label-success"><?php echo $value['required']?></span></td>
  			<td class="txtc"><span class="label label-<?php echo $clz?>"><?php echo $value['checked']?></span></td>
  		</tr>
  		<?php endforeach;?>  		
  	</tbody>
  </table>
</div>
<div class="panel panel-default">
  <div class="panel-heading">PHP环境检测</div>  
  <table class="table table-striped">
  	<thead>
  		<tr>
  			<th>检测项目</th>
  			<th width="100" class="txtc">要求</th>
  			<th width="100" class="txtc">当前</th>
  		</tr>
  	</thead>
  	<tbody>
  		<?php 
  			foreach($exts as $key => $value):
  				$clz = $value['pass']?'success':'danger';
  				$passCheck &= ($value['required'] == '可选' || $value['pass']);
  		?>  		
  		<tr>
  			<td><?php echo $key?> </td>
  			<td class="txtc"><span class="label label-success"><?php echo $value['required']?></span></td>
  			<td class="txtc"><span class="label label-<?php echo $clz?>"><?php echo $value['checked']?></span></td>
  		</tr>
  		<?php endforeach;?>  		
  	</tbody>
  </table>
</div>
<div class="row">
  <div class="col-md-3 col-md-offset-9 txtr">
  	<?php if($passCheck):?>
  		<button id="nextStep" class="btn btn-success" onclick="KissgoInstaller.viewPage('setup',true)">下一步</button>
  	<?php else:?>
  		<button class="btn btn-warning" onclick="KissgoInstaller.viewPage('env')">重新检测</button>
  	<?php endif;?>
  </div>  
</div>

<?php }else if($step == 'setup'){
	$data = sess_get('install_setup_data',array('clean_url'=>true,'gzip'=>extension_loaded ( "zlib" )));
	?>	
<form class="form-horizontal" role="form" id="setupForm">	
	<div class="form-group">
		<label for="site_name" class="col-sm-3 control-label">网站名称 <em class="text-danger">*</em></label>
		<div class="col-sm-7">
			<input type="text" class="form-control" id="site_name" name="site_name" placeholder="网站名称" value="<?php echo $data['site_name']?>"/>
			<span class="help-block">请填写您的网站的名称。</span>		
		</div>
	</div>
	
	<div class="form-group">
		<label for="gzip" class="col-sm-3 control-label">启用输出压缩 </label>
		<div class="col-sm-7">
			<div class="checkbox">
				<label>
					<input type="checkbox" id="gzip" name="gzip" <?php if($data['gzip']) echo ' checked="checked"'?>/> 启用输出压缩以节省网络流利与加快传输速度
				</label>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<label for="clean_url" class="col-sm-3 control-label">启用伪静态 </label>
		<div class="col-sm-7">
			<div class="checkbox">
				<label>
					<input type="checkbox" id="clean_url" name="clean_url" <?php if($data['clean_url']) echo ' checked="checked"'?>/> 如果您的服务器支持,建议开启.
				</label>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<label for="email" class="col-sm-3 control-label">您的邮箱  <em class="text-danger">*</em></label>
		<div class="col-sm-7">
			<input type="text" class="form-control" id="email" name="email" placeholder="您的邮箱" value="<?php echo $data['email']?>"/>
			<span class="help-block">请填写您的Email地址。这将是本站的超级管理员的Email地址。</span>		
		</div>
	</div>
	<div class="form-group">
		<label for="name" class="col-sm-3 control-label">管理员账号 <em class="text-danger">*</em></label>
		<div class="col-sm-7">
			<input type="text" class="form-control" id="name" name="name" placeholder="管理员账号" value="<?php echo $data['name']?>"/>
			<span class="help-block">设置您网站超级管理员的用户名。</span>		
		</div>
	</div>
	<div class="form-group">
		<label for="passwd" class="col-sm-3 control-label">管理员密码 <em class="text-danger">*</em></label>
		<div class="col-sm-7">
			<input type="password" class="form-control" id="passwd" name="passwd"/>
			<span class="help-block">设置该超级管理员帐号的密码，并在下面予以确定。</span>		
		</div>
	</div>
	<div class="form-group">
		<label for="passwd1" class="col-sm-3 control-label">确认管理员密码</label>
		<div class="col-sm-7">
			<input type="password" class="form-control" id="passwd1" name="passwd1"/>				
		</div>
	</div>
	<div class="form-group">
		<label for="dashboardURL" class="col-sm-3 control-label">管理后台地址</label>
		<div class="col-sm-7">
			<input type="text" class="form-control" id="urlm" name="urlm" placeholder="管理后台地址" value="<?php echo $data['urlm']?>"/>
			<span class="help-block">不填写时使用默认值backend，为了安全强烈建议修改一个不常用值。</span>		
		</div>
	</div>
</form>
<div class="row">
  <div class="col-md-3 col-md-offset-7 txtr">
    <button id="nextStep1" class="btn btn-default" onclick="KissgoInstaller.loadPage('env','setup')">上一步</button>	
  	<button id="nextStep" class="btn btn-success" onclick="KissgoInstaller.loadPage('database','setup')">下一步</button>  	
  </div>  
</div>
<?php }else if($step == 'database'){
$drivers = array();
if(extension_loaded('pdo_mysql')){
	$drivers['MySQL'] = 'MySQL';	
}
if(extension_loaded('pdo_pgsql')){
	$drivers['PostgreSQL'] = 'PostgreSQL';
}
$data = sess_get('install_database_data',array('host'=>'localhost'));
?>
<form class="form-horizontal" role="form" id="databaseForm" name="InstallForm">
	<div class="form-group">
		<label for="driver" class="col-sm-3 control-label">数据库驱动 <em class="text-danger">*</em></label>
		<div class="col-sm-7">
			<select class="form-control" id="driver" name="driver">
				<?php foreach ($drivers as $k=>$v):?>
				<option value="<?php echo $k?>" <?php echo ($k==$data['driver'])?'selected="selected"':''?>><?php echo $v?></option>
				<?php endforeach;?>
			</select>
			<span class="help-block">使用何种方式访问数据库。</span>		
		</div>
	</div>
	
	<div class="form-group">
		<label for="host" class="col-sm-3 control-label">主机地址 <em class="text-danger">*</em></label>
		<div class="col-sm-7">
			<input type="text" class="form-control" id="host" name="host" placeholder="主机地址" value="<?php echo $data['host']?>"/>
			<span class="help-block">你的数据库服务器的主机名或IP地址，该设置通常是 "localhost"。 </span>		
		</div>
	</div>
	
	<div class="form-group">
		<label for="port" class="col-sm-3 control-label">主机端口</label>
		<div class="col-sm-7">
			<input type="text" class="form-control" id="port" name="port" placeholder="主机端口" value="<?php echo $data['port']?>"/>
			<span class="help-block">你的数据库服务器监听的端口，一般不填写使用默认值。 </span>		
		</div>
	</div>
	<div class="form-group">
		<label for="dbuser" class="col-sm-3 control-label">数据库用户 <em class="text-danger">*</em></label>
		<div class="col-sm-3">
			<input type="text" class="form-control" id="dbuser" name="dbuser" placeholder="数据库用户" value="<?php echo $data['dbuser']?>"/>
			<span class="help-block">您的数据库用户名 。 </span>		
		</div>	
		<label for="passwd" class="col-sm-2 control-label">用户的密码 <em class="text-danger">*</em></label>
		<div class="col-sm-2">
			<input type="password" class="form-control" id="passwd" name="passwd" placeholder="用户的密码"/>
			<span class="help-block">您的数据库用户的密码 。 </span>		
		</div>
	</div>
	<div class="form-group">
		<label for="dbname" class="col-sm-3 control-label">数据库名 <em class="text-danger">*</em></label>
		<div class="col-sm-7">
			<input type="text" class="form-control" id="dbname" name="dbname" placeholder="数据库" value="<?php echo $data['dbname']?>"/>
			<span class="help-block">wulacms将被安装到这个数据库。 </span>		
		</div>
	</div>
	<div class="form-group">
		<label for="prefix" class="col-sm-3 control-label">表前缀</label>
		<div class="col-sm-7">
			<input type="text" class="form-control" id="prefix" name="prefix" placeholder="表前缀"  value="<?php echo $data['prefix']?>"/>
			<span class="help-block">使用表前缀可将在一个数据库中安装多个wulacms系统。 </span>		
		</div>
	</div>
</form>
<div class="row">
  <div class="col-md-3 col-md-offset-7 txtr">
    <button id="nextStep1" class="btn btn-default" onclick="KissgoInstaller.loadPage('setup','database')">上一步</button>	
  	<button id="nextStep" class="btn btn-success" onclick="KissgoInstaller.loadPage('overview','database')">下一步</button>  	
  </div>  
</div>
<?php } else if($step == 'overview'){
	$setup = sess_get('install_setup_data',array());
	$db = sess_get('install_database_data',array());
	?>
<div class="panel panel-default">
  <div class="panel-heading">配置</div>  
  <table class="table table-striped">
  	<thead>
  		<tr>
  			<th>配置项目</th>
  			<th width="60%">配置</th>  			
  		</tr>
  	</thead>
  	<tbody>  			
  		<tr><td>网站名称</td><td><?php echo $setup['site_name']?></td></tr>
  		<tr><td>启用输出压缩</td><td><?php echo $setup['gzip']?'开启':'关闭'?></td></tr>
  		<tr><td>启用伪静态</td><td><?php echo $setup['clean_url']?'开启':'关闭'?></td></tr>
  		<tr><td>您的邮箱</td><td><?php echo $setup['email']?></td></tr>
  		<tr><td>管理员账号</td><td><?php echo $setup['name']?></td></tr>
  		<tr><td>管理员密码</td><td>******</td></tr>
  		<tr><td>管理后台地址</td><td><?php echo $setup['urlm']?></td></tr>  		
  	</tbody>
  </table>
</div>
<div class="panel panel-default">
  <div class="panel-heading">数据库配置</div>  
  <table class="table table-striped">
  	<thead>
  		<tr>
  			<th>配置项目</th>
  			<th width="60%">配置</th>  			
  		</tr>
  	</thead>
  	<tbody>  			
  		<tr><td>数据库驱动</td><td><?php echo $db['driver']?></td></tr>
  		<tr><td>主机地址</td><td><?php echo $db['host']?></td></tr>
  		<tr><td>主机端口</td><td><?php echo $db['port']?$db['port']:'默认'?></td></tr>
  		<tr><td>数据库用户</td><td><?php echo $db['dbuser']?></td></tr>
  		<tr><td>用户的密码</td><td>******</td></tr>
  		<tr><td>数据库名</td><td><?php echo $db['dbname']?></td></tr>
  		<tr><td>表前缀</td><td><?php echo $db['prefix']?></td></tr>
  	</tbody>
  </table>
</div>
<div class="row">
  <div class="col-md-3 col-md-offset-9 txtr">
    <button id="nextStep1" class="btn btn-default" onclick="KissgoInstaller.loadPage('database','overview')">上一步</button>	
  	<button id="nextStep" class="btn btn-success" onclick="KissgoInstaller.loadPage('install','overview')">安装</button>  	
  </div>  
</div>
<?php } else if($step == 'install'){?>
<div class="page-header">
  <h1>安装中... <small id="process">0%</small></h1>    
</div>
<div class="progress">
	<div class="progress-bar" id="progressbar"  style="width:0px;"></div>
</div>
<table class="table">
  <thead>
  	<tr><th>安装进程</th><th width="100" class="txtr">状态</th></tr>
  </thead>
  <tbody id="result">
  	<tr class="status_init"><td>初始化安装程序</td><td class="status"></td></tr>
  </tbody>  
</table>
<?php } else if($step == 'done'){
		$error = sess_get('install_done',false);
		$msg   = sess_get('msg','');		
		$site_home = '../';
		$setup = sess_get('install_setup_data',array());
		$admin_url = $site_home.(empty($setup['urlm'])?'backend/':$setup['urlm']);
		if($error !== true):
	?>
<div class="alert alert-danger">
	<h4>靠! 安装出错了!</h4>
	<p><?php echo $msg?></p>
</div>
<?php else:
@unlink($lock_file);
@$_SESSION['installing'] = null;
@$_SESSION['msg'] = null;
@file_put_contents(APPDATA_PATH.'install.lock', time());
?>
<div class="alert alert-success">
	<h4>哇，恭喜! 安装完成啦!</h4>
	<p>已经成功安装您的网站，请立即删除install目录。</p>
</div>
<div class="row">
  <div class="col-md-4">
    <a id="nextStep1" class="btn btn-default" href="<?php echo $site_home?>">查看网站</a>	
  	<a id="nextStep" class="btn btn-success" href="<?php echo $admin_url?>">后台管理</a>  	
  </div>  
</div>
<?php endif;
} else if($step == 'save'){
	$op = rqst('op');
	$data  = array('success'=>true);
	$form = null;
	if($op == 'setup'){
		$form = new SetupForm();
		$formData = $form->valid();	
		if($formData){
			$formData['gzip'] = rqst('gzip',true);
			$formData['clean_url'] = rqst('clean_url',true);
		}	
	}else if($op == 'database'){
		$form = new DatabaseForm();		
		$formData = $form->valid();		
	}
	if(!$formData){
		$data['success'] = false;
		$data['errors'] = $form->getErrors();
	}else{
		@$_SESSION['install_'.$op.'_data'] = $formData;
	}
	if($op == 'database' && $formData){
		$cnf = $formData;
		unset($cnf['dbname']);
		$cnf['user'] = $cnf['dbuser'];
		$cnf['password'] = $cnf['passwd'];
		$dialect = DatabaseDialect::getDialect($cnf);
		if($dialect == null){
			$data['success'] = false;
			$data['msg'] = DatabaseDialect::$lastErrorMassge;
		}else{
			$ver = $dialect->getAttribute(PDO::ATTR_SERVER_VERSION);
			if(version_compare('5.6.20', $ver,'>')){
				$data['success'] = false;
				$data['msg'] = '你的数据库版本为'.$ver.',本程序要求数据库的最低版本为5.6.20.';
			}
		}
	}
	echo json_encode($data);
} else if($step == 'process'){
	$op = rqst('op');
	$setup = sess_get('install_setup_data',array('clean_url'=>true,'gzip'=>true));
	$db = sess_get('install_database_data',array('host'=>'localhost'));
	$progres  = new KissgoInstaller($op,$setup,$db);
	$progres  = $progres->install();
	echo json_encode($progres);
} else { ?>
	<p class="txtc">未知操作！</p>
<?php }?>