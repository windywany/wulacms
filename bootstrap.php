<?php
/*
 * kissgo framework that keep it simple and stupid, go go go ~~ @author Leo Ning @package includes $Id$
 */
defined ( 'WEB_ROOT' ) or header ( 'HTTP/1.1 404 Not Found', true, 404 ) or exit ();
// ////////////////////////////////////////////////////////////////////////
// common constant
define ( 'DS', DIRECTORY_SEPARATOR ); // the short for directory separator
define ( 'APP_PATH', WEB_ROOT );
defined ( 'WEBAPP_PATH' ) or define ( 'WEBAPP_PATH', WEB_ROOT );
define ( 'KIS_START_TIME', time () );
define ( 'KIS_INIT_MEMORY', memory_get_usage ( true ) );
// ////////////////////////////////////////////////////////////////////////
// path constant
defined ( 'APP_NAME' ) or define ( 'APP_NAME', basename ( WEB_ROOT ) );
defined ( 'APPDATA_DIR' ) or define ( 'APPDATA_DIR', 'appdata' );
defined ( 'APPDATA_PATH' ) or define ( 'APPDATA_PATH', APP_PATH . APPDATA_DIR . DS );
defined ( 'TMP_PATH' ) or define ( 'TMP_PATH', APPDATA_PATH . 'tmp' . DS );

defined ( 'MODULE_DIR' ) or define ( 'MODULE_DIR', 'modules' );
defined ( 'EXTENSION_DIR' ) or define ( 'EXTENSION_DIR', 'extensions' );
defined ( 'THEME_DIR' ) or define ( 'THEME_DIR', 'themes' );
defined ( 'MISC_DIR' ) or define ( 'MISC_DIR', 'assets' );

define ( 'MODULES_PATH', WEBAPP_PATH . MODULE_DIR . DS );
define ( 'EXTENSIONS_PATH', WEBAPP_PATH . EXTENSION_DIR . DS );
define ( 'THEME_PATH', WEBAPP_PATH );
define ( 'INCLUDES', WEB_ROOT . 'includes' . DS );
define ( 'KISSGO', INCLUDES . 'core' . DS );
// ////////////////////////////////////////////////////////////////////////
// debug levels
define ( 'DEBUG_ERROR', 5 );
define ( 'DEBUG_INFO', 4 );
define ( 'DEBUG_WARN', 3 );
define ( 'DEBUG_DEBUG', 2 );
define ( 'DEBUG_OFF', 6 );
@ob_start ();
// 过滤输入
if (@ini_get ( 'register_globals' )) {
	die ( 'please close "register_globals" in php.ini file.' );
}
if (version_compare ( '5.3', phpversion (), '>' )) {
	die ( sprintf ( 'Your php version is %s,but kissgo required  php 5.3+', phpversion () ) );
}
if (! defined ( 'RUNTIME_MEMORY_LIMIT' )) {
	define ( 'RUNTIME_MEMORY_LIMIT', '128M' );
}
if (function_exists ( 'memory_get_usage' ) && (( int ) @ini_get ( 'memory_limit' ) < abs ( intval ( RUNTIME_MEMORY_LIMIT ) ))) {
	@ini_set ( 'memory_limit', RUNTIME_MEMORY_LIMIT );
}

if (function_exists ( 'mb_internal_encoding' )) {
	mb_internal_encoding ( 'UTF-8' );
	mb_regex_encoding ( 'UTF-8' );
}
if (version_compare ( phpversion (), '5.3', '<' )) {
	@set_magic_quotes_runtime ( 0 );
}
@ini_set ( 'magic_quotes_sybase', 0 );
@ini_set ( 'session.bug_compat_warn', 0 );
global $_kissgo_processing_installation;
include INCLUDES . 'classes' . DS . 'KissGoSetting.php';
$_ksg_settings_file = APPDATA_PATH . 'settings.php';
if (is_file ( $_ksg_settings_file )) {
	if (is_file ( APPDATA_PATH . 'install.lock' ) && file_exists ( WEB_ROOT . 'install' )) {
		die ( 'for security, please delete install directory!' );
	}
	$_ksg_cluster_settings_file = APPDATA_PATH . 'cluster_settings.php';
	if (file_exists ( $_ksg_cluster_settings_file )) {
		include $_ksg_cluster_settings_file;
	}
	include $_ksg_settings_file;
	KissGoSetting::prepareDefaultSetting ();
} else if ($_kissgo_processing_installation != true) {
	$install_script = KissGoSetting::detectBaseUrl () . 'install/install.php';
	echo "<html><head><script type='text/javascript'>var win = window;while (win.location.href != win.parent.location.href) {win = win.parent;} win.location.href = '{$install_script}';</script></head><body></body></html>";
	exit ();
} else if ($_kissgo_processing_installation) {
	$_ksg_settings_file = APPDATA_PATH . 'default.settings.php';
	if (is_file ( $_ksg_settings_file )) {
		include $_ksg_settings_file;
		KissGoSetting::prepareDefaultSetting ();
	} else {
		die ( 'default.settings.php is not found, installation cannot be performed!' );
	}
}
unset ( $_ksg_settings_file );
defined ( 'DEBUG' ) or define ( 'DEBUG', DEBUG_DEBUG );
if (DEBUG == DEBUG_OFF) {
	define ( 'KS_ERROR_REPORT_LEVEL', E_ALL & ~ E_NOTICE & ~ E_DEPRECATED & ~ E_STRICT & ~ E_WARNING );
	@ini_set ( 'display_errors', 0 );
} else if (DEBUG > DEBUG_DEBUG) {
	define ( 'KS_ERROR_REPORT_LEVEL', E_ALL & ~ E_NOTICE & ~ E_DEPRECATED & ~ E_STRICT );
	@ini_set ( 'display_errors', 1 );
} else {
	define ( 'KS_ERROR_REPORT_LEVEL', E_ALL & ~ E_NOTICE );
	@ini_set ( 'display_errors', 1 );
}
error_reporting ( KS_ERROR_REPORT_LEVEL );
if (defined ( 'DEBUG_FIREPHP' ) && DEBUG_FIREPHP) {
	include INCLUDES . 'vendors/firephp/fb.php';
}
// 时区设置
defined ( 'TIMEZONE' ) or define ( 'TIMEZONE', 'Asia/Shanghai' );
// ////////////////////////////////////////////////////////////////////////
// 定义URL常量
if (! isset ( $_SERVER ['HTTP_HOST'] )) {
	$_SERVER ['HTTP_HOST'] = 'localhost';
}
$_h = explode ( ':', $_SERVER ['HTTP_HOST'] );
define ( 'REAL_HTTP_HOST', array_shift ( $_h ) );
define ( 'DETECTED_URL', KissGoSetting::detectBaseUrl () );
define ( 'DETECTED_ABS_URL', KissGoSetting::detectBaseUrl ( true ) );
define ( 'MODULE_URL', DETECTED_URL . MODULE_DIR . '/' );
define ( 'THEME_URL', DETECTED_URL . THEME_DIR . '/' );
define ( 'ASSETS_URL', DETECTED_URL . MISC_DIR . '/' );

$CRUDQ_HTTP_HOST = explode ( '.', $_SERVER ['HTTP_HOST'] );
define ( 'KSG_SITE_HOST', $_SERVER ['HTTP_HOST'] );
define ( 'KSG_APPID', md5 ( $_SERVER ['HTTP_HOST'] ) );

if (count ( $CRUDQ_HTTP_HOST ) >= 3) {
	define ( 'KSG_SUBDOMAIN', array_shift ( $CRUDQ_HTTP_HOST ) );
} else {
	define ( 'KSG_SUBDOMAIN', 'www' );
}
define ( 'KSG_DOMAIN', implode ( '.', $CRUDQ_HTTP_HOST ) );
date_default_timezone_set ( TIMEZONE );

// ////////////////////////////////////////////////////////////////////////
// include common files
include KISSGO . 'version.php';
include KISSGO . 'i18n.php';
include KISSGO . 'functions.php';
include KISSGO . 'template.php';
include KISSGO . 'session.php';
include KISSGO . 'views.php';
include KISSGO . 'phpcrud/phpcrud.php';
include KISSGO . 'rbac.php';
include KISSGO . 'plugin.php';
include KISSGO . 'cache.php';
// ////////////////////////////////////////////////////////////////////////
// 类自动加载
$__kissgo_exports [] = KISSGO . 'phpcrud';
$__kissgo_exports [] = KISSGO . 'phpcrud' . DS . 'dialects';
$__kissgo_exports [] = INCLUDES . 'classes';
$__kissgo_exports [] = INCLUDES . 'classes' . DS . 'form';
$__kissgo_exports [] = INCLUDES . 'classes' . DS . 'view';
$__kissgo_exports [] = INCLUDES . 'classes' . DS . 'io';
$__kissgo_exports [] = INCLUDES . 'classes' . DS . 'utils';
$__kissgo_exports [] = INCLUDES . 'vendors';
spl_autoload_register ( '_kissgo_class_loader' );
$composer_loader_file = WEB_ROOT . 'vendor' . DS . 'autoload.php';
if (is_file ( $composer_loader_file )) {
	include $composer_loader_file;
}
unset ( $composer_loader_file );
if ($_kissgo_processing_installation != true) {
	$__ksg_module_path = array ();
	// 加载模块
	$modules = RtCache::get ( 'app_list' );
	$exports = RtCache::get ( 'class_exports' );
	if (! $modules) {
		$hd = opendir ( MODULES_PATH );
		if ($hd) {
			$exports = array ();
			while ( ($f = readdir ( $hd )) != false ) {
				if (is_dir ( MODULES_PATH . $f ) && $f != '.' && $f != '..') {
					$app = MODULES_PATH . $f . DS . $f . '.php';
					if (is_file ( $app )) {
						$modules [$f] = $app;
					}
					$fp = MODULES_PATH . $f . DS . 'classes';
					if (is_dir ( $fp )) {
						$exports [] = $fp;
					}
				}
			}
			@closedir ( $hd );
			if (DEBUG > DEBUG_DEBUG) {
				RtCache::add ( 'app_list', $modules );
				RtCache::add ( 'class_exports', $exports );
			}
		}
	}
	if ($exports) {
		$__kissgo_exports = array_merge ( $__kissgo_exports, $exports );
	}
	if ($modules) {
		$apps = RtCache::get ( 'apps_installation' );
		if (! $apps) {
			$apps = dbselect ( 'urlmapping,app' )->from ( '{apps}' )->where ( array ('status' => 1 ) )->asc('app')->toArray ( 'urlmapping', 'app' );
			if (DEBUG > DEBUG_DEBUG) {
				RtCache::add ( 'apps_installation', $apps );
			}
		}
		foreach ( $modules as $f => $m ) {
			if (isset ( $apps [$f] )) {
				$__ksg_module_path [$f] = dirname ( $m ) . DS;
				if ($apps [$f] != $f) {
					Router::map ( $apps [$f], $f );
				}
				include $m;
			}
		}
		$__kissgo_apps = $apps;
		unset ( $apps );
	}
	//load extensions
	$extensions = RtCache::get ( 'ext_list' );
	if (! $extensions && is_dir(EXTENSIONS_PATH)) {
		$hd = opendir ( EXTENSIONS_PATH );
		if ($hd) {
			while ( ($f = readdir ( $hd )) != false ) {
				if (is_dir ( EXTENSIONS_PATH . $f ) && $f != '.' && $f != '..') {
					$app = EXTENSIONS_PATH . $f . DS . $f . '.php';
					if (is_file( $app )) {
						$extensions [$f] = $app;
					}
				}
			}
			@closedir ( $hd );
			if (DEBUG > DEBUG_DEBUG) {
				RtCache::add ( 'ext_list', $extensions );
			}
		}
	}
	if ($extensions) {
		foreach ( $extensions as $f => $m ) {
			include $m;
		}
	}
	unset ( $modules, $exports, $extensions, $m, $fp, $app, $e, $hd, $f, $settings, $CRUDQ_HTTP_HOST, $_h );
	if (isset ( $_GET ['_url'] )) {
		define ( 'REQUEST_URL', Request::xss_clean ( $_GET ['_url'] ) );
		unset ( $_GET ['_url'] );
	} else if (isset ( $_SERVER ['REQUEST_URI'] )) {
		define ( 'REQUEST_URL', explode ( '?', $_SERVER ['REQUEST_URI'] ) [0] );
	}
	// you can do somthing to improve your application performance in APPDATA_PATH/cache.php file.
	// but in this file, you just can use little functionality Kissgo provided.
	// you might write this script carefully so that it will not slow down your APP.
	if (defined ( 'CACHE_ENABLED' ) && CACHE_ENABLED && file_exists ( APPDATA_PATH . 'cache.php' )) {
		include APPDATA_PATH . 'cache.php';
	}
	// requst
	$__rqst = false;
	if(class_exists('\Notoj\Notoj')){
		if(DEBUG > DEBUG_DEBUG) {
			\Notoj\Notoj::enableCache(TMP_PATH . 'annotations.php');
		}
		define('ANNOTATION_SUPPORT',true);
	}else{
		define('ANNOTATION_SUPPORT',false);
	}
	// modules loaded
	fire ( 'engine_initialized' );
	if (defined ( 'R_UUID_ENABLED' ) && R_UUID_ENABLED) {
		Request::setUUID ();
	}
} else {
	$__rqst = Request::getInstance ();
	$__rqst->startSession ();
}
// end of file bootstrap.php