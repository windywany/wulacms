<?php
/**
 * 抽象的程序安装基类，应用程序安装器应该完成自身的安装与升级，并将结果通知系统.
 *
 * @author Ning Guangfeng <windywany@gmail.com>
 * @package classes
 */
abstract class AppInstaller {
	protected $errorMsg = '';
	protected $name;
	public function __construct() {
		$this->name = strtolower ( str_replace ( 'Installer', '', get_class ( $this ) ) );
	}
	public static function clearAppCache() {
		RtCache::delete ( 'app_list' );
		RtCache::delete ( 'class_exports' );
		RtCache::delete ( 'apps_installation' );
	}
	/**
	 * get the appInstaller object.
	 *
	 * @param string $app        	
	 * @return AppInstaller null
	 */
	public final static function getAppInstaller($app) {
		$appClz = ucfirst ( $app ) . 'Installer';
		if (self::hasInstaller ( $app )) {
			return new $appClz ();
		}
		return null;
	}
	public final static function getApps($installed = true, $status = 'installed') {
		$installedApps = dbselect ( '*' )->from ( '{apps}' )->toArray ( null, 'app' );
		$allApps = AppInstaller::listApps ();
		$apps = array ();
		foreach ( $allApps as $name => $app ) {
			$app = $app->toArray ();
			$app ['app'] = $name;
			$app ['status'] = 1;
			$app ['system'] = 0;
			$app ['installed'] = false;
			$app ['forupdating'] = 0;
			if (isset ( $installedApps [$name] )) {
				$iapp = $installedApps [$name];
				$app ['installedVer'] = $iapp ['version'];
				$app ['upgradable'] = version_compare ( $app ['currentVer'], $app ['installedVer'], '>' ) && $iapp ['status'];
				$app ['forupdating'] = $app ['upgradable'] ? 1 : 0;
				$app ['status'] = $iapp ['status'];
				
				$app ['installed'] = true;
				$app ['system'] = isset ( $iapp ['system'] ) ? $iapp ['system'] : 0;
				$app ['urlmapping'] = $iapp ['urlmapping'];
				if (isset ( $installedApps ['cms'] )) {
					$key = md5 ( $iapp ['urlmapping'] . '/index.html' );
					$app ['conflict'] = dbselect ()->from ( '{cms_page}' )->where ( array ('deleted' => 0,'url_Key' => $key ) )->exist ( 'id' );
				} else {
					$app ['conflict'] = false;
				}
			}
			if ($installed && isset ( $installedApps [$name] )) {
				$apps [] = $app;
			} else if (! $installed && ! isset ( $installedApps [$name] )) {
				$apps [] = $app;
			}
		}
		$others = array ();
		if ($installed) {
			foreach ( $apps as $app ) {
				if (! $app ['status'] && $status == 'disabled') {
					$others [] = $app;
				} else if ($app ['status'] && $app ['upgradable'] && $status == 'upgrade') {
					$others [] = $app;
				} else if ($app ['status'] && $status == 'system' && $app ['system']) {
					$others [] = $app;
				} else if ($app ['status'] && $status == 'installed' && ! $app ['system']) {
					$others [] = $app;
				}
			}
			$apps = $others;
		}
		uasort ( $apps, ArrayComparer::str ( 'app' ) );
		
		return $apps;
	}
	public final static function hasInstaller($app) {
		$appClz = ucfirst ( $app ) . 'Installer';
		$f = MODULES_PATH . $app . DS . $appClz . '.php';
		if (is_file ( $f )) {
			include_once $f;
			if (is_subclass_of2 ( $appClz, 'AppInstaller' )) {
				return true;
			}
		}
		return false;
	}
	public function getLastError() {
		return $this->errorMsg;
	}
	public function setLastError($error) {
		$this->errorMsg = $error;
	}
	/**
	 * install this application.
	 *
	 * @return boolean
	 */
	public function onInstall($dialect) {
		$dps = $this->getDependences ();
		if ($dps) {
			$rst = AppInstaller::checkDependences ( $dps, $dialect );
			if ($rst !== true) {
				$this->errorMsg = $rst;
				return false;
			}
		}
		$verlist = $this->getVersionLists ();
		foreach ( $verlist as $ver => $build ) {
			if (method_exists ( $this, 'upgradeTo' . $build )) {
				if (! call_user_func_array ( array ($this,'upgradeTo' . $build ), array ($dialect ) )) {
					return false;
				}
			}
		}
		return true;
	}
	/**
	 * update this application from one version to another version.
	 *
	 * @param string $fromVer        	
	 * @param string $toVer        	
	 * @param DatabaseDialect $dialect        	
	 */
	public final function upgrade($fromVer, $toVer, $dialect) {
		$dps = $this->getDependences ();
		if ($dps) {
			$rst = AppInstaller::checkDependences ( $dps, $dialect );
			if ($rst !== true) {
				$this->errorMsg = $rst;
				return false;
			}
		}
		$verlist = $this->getVersionLists ();
		if (! $verlist) {
			$this->errorMsg = '版本列表为空.';
			return false;
		}
		$sqls = $this->getSqls ( $dialect->getDriverName (), $toVer, $fromVer );
		$rst = AppInstaller::execSql ( $dialect, $sqls );
		if ($rst === true) {
			foreach ( $verlist as $ver => $build ) {
				if (version_compare ( $ver, $toVer, '<=' ) && version_compare ( $ver, $fromVer, '>' )) {
					if (method_exists ( $this, 'upgradeTo' . $build )) {
						if (! call_user_func_array ( array ($this,'upgradeTo' . $build ), array ($dialect ) )) {
							return false;
						}
					}
				}
			}
			$user = whoami ();
			$data ['user_id'] = $user->getUid ();
			$data ['update_time'] = time ();
			$data ['version'] = $toVer;
			return dbupdate ( '{apps}' )->set ( $data )->where ( array ('app' => $this->name ) )->exec ();
		} else {
			$this->errorMsg = $rst;
			return false;
		}
	}
	/**
	 * 应用唯一ID.
	 *
	 * @return string
	 */
	public final function getID() {
		return $this->name;
	}
	/**
	 * 依赖模块列表.
	 *
	 * @return array
	 */
	public function getDependences() {
		return array ();
	}
	/**
	 * 检查PHP扩展与环境.
	 *
	 * @param array $checked        	
	 */
	public function checkPhpExtention(&$checked) {
	}
	/**
	 * 检测文件权限.
	 *
	 * @param array $files        	
	 */
	public function checkFilePermission(&$files) {
	}
	/**
	 * 卸载。
	 *
	 * @return boolean
	 */
	public function uninstall() {
		$tables = $this->getDefinedTables ( DatabaseDialect::getDialect () );
		if ($tables ['tables']) {
			foreach ( $tables ['tables'] as $table ) {
				dbexec ( 'DROP TABLE IF EXISTS ' . $table );
			}
		}
		if ($tables ['views']) {
			foreach ( $tables ['views'] as $table ) {
				dbexec ( 'DROP VIEW IF EXISTS ' . $table );
			}
		}
		return true;
	}
	/**
	 * 取应用已经安装的版本号.
	 *
	 * @return Ambigous <Ambigous, NULL, unknown, multitype:>|boolean
	 */
	public final function getInstalledVersion() {
		$version = dbselect ()->from ( '{apps}' )->where ( array ('app' => $this->name ) )->get ( 'version' );
		if ($version) {
			return $version;
		} else {
			$this->errorMsg = '无法获取此应用已经安装的版本号.';
			return false;
		}
	}
	/**
	 * 取此模块的URL MAPPING.
	 *
	 * @return URL MAPPING.
	 */
	public final function getUrlMap() {
		$urlmapping = dbselect ()->from ( '{apps}' )->where ( array ('app' => $this->name ) )->get ( 'urlmapping' );
		if ($urlmapping) {
			return $urlmapping;
		} else {
			return $this->name;
		}
	}
	/**
	 * obtain the sql for install or alter the database.
	 *
	 * @param string $driver        	
	 * @param string $version        	
	 * @return NULL
	 */
	public function getSqls($driver, $toVer, $fromVer = '0.0.0') {
		$tables = array ();
		$sqlFile = MODULES_PATH . $this->name . DS . 'sqls' . DS . 'db_' . $driver . '.sql.php';
		if (file_exists ( $sqlFile )) {
			include_once $sqlFile;
			$sqls = array ();
			if (! empty ( $tables )) {
				foreach ( $tables as $ver => $var ) {
					if (version_compare ( $ver, $toVer, '<=' ) && version_compare ( $ver, $fromVer, '>' )) {
						$sqls = array_merge ( $sqls, $var );
					}
				}
			}
		}
		return $sqls;
	}
	/**
	 * 取当前模板所定义的表.
	 *
	 * @param DatabaseDialect $dialect        	
	 */
	public function getDefinedTables($dialect) {
		$sqlFile = MODULES_PATH . $this->name . DS . 'sqls' . DS . 'db_' . $dialect->getDriverName () . '.sql.php';
		if (file_exists ( $sqlFile )) {
			$file = file_get_contents ( $sqlFile );
			return $dialect->getTablesFromSQL ( $file );
		}
		return array ();
	}
	/**
	 * 执行sql。
	 *
	 * @param DatabaseDialect $dialect        	
	 * @param array $sqls        	
	 * @return Ambigous <mixed, boolean>|boolean
	 */
	public final static function execSql($dialect, $sqls) {
		if (is_array ( $sqls ) && ! empty ( $sqls )) {
			$rst = false;
			foreach ( $sqls as $table ) {
				$table = str_replace ( '{prefix}', $dialect->getTablePrefix (), $table );
				$rst = dbexec ( $table, $dialect );
				if (! $rst) {
					$rst = DatabaseDialect::$lastErrorMassge;
					break;
				}
			}
			return $rst;
		} else {
			return true;
		}
	}
	/**
	 * 检测文件权限.
	 *
	 * @param string $f
	 *        	文件路径.
	 * @param string $r
	 *        	读
	 * @param string $w
	 *        	写
	 * @return array
	 */
	public final static function checkFile($f, $r = true, $w = true) {
		$rst = array ();
		$checked = $required = '';
		if ($r) {
			$required .= '可读';
		}
		if ($w) {
			$required .= '可写';
		}
		if (file_exists ( $f )) {
			if ($r) {
				$checked = is_readable ( $f ) ? '可读' : '不可读';
			}
			if ($w) {
				if (is_dir ( $f )) {
					$len = @file_put_contents ( $f . '/test.dat', 'test' );
					if ($len > 0) {
						@unlink ( $f . '/test.dat' );
						$checked .= '可写';
					} else {
						$checked .= '不可写';
					}
				} else {
					$checked .= is_writable ( $f ) ? '可写' : '不可写';
				}
			}
		} else {
			$checked = '不存在';
		}
		$rst ['required'] = $required;
		$rst ['checked'] = $checked;
		$rst ['pass'] = $checked == $required;
		return $rst;
	}
	/**
	 * 读取所有应用.
	 *
	 * @return array
	 */
	public final static function listApps() {
		$apps = array ();
		$appdir = opendir ( MODULES_PATH );
		if ($appdir) {
			while ( ($app = readdir ( $appdir )) != false ) {
				if ($app != '.' && $app != '..') {
					$appIns = self::getAppInstaller ( $app );
					if ($appIns != null) {
						$apps [$app] = $appIns;
					}
				}
			}
			closedir ( $appdir );
		}
		return $apps;
	}
	public final static function checkDependences($apps, $dialect) {
		if (empty ( $apps )) {
			return true;
		}
		$iapps = dbselect ( 'app,version' )->from ( '{apps}' )->where ( array ('status' => 1 ) )->setDialect ( $dialect )->toArray ( 'version', 'app' );
		$alapps = self::listApps ();
		foreach ( $apps as $app => $version ) {
			if (isset ( $iapps [$app] )) {
				list ( $min, $minop, $max, $maxop ) = parse_version_pair ( $version );
				if ($min && ! version_compare ( $min, $iapps [$app], $minop )) {
					return '当前的模块"' . $alapps [$app]->getName () . '"版本太低.';
				}
				if ($max && ! version_compare ( $max, $iapps [$app], $maxop )) {
					return '当前的模块"' . $alapps [$app]->getName () . '"版本太高.';
				}
			} else if (isset ( $alapps [$app] )) {
				return '请先安装或启用模块"' . $alapps [$app]->getName () . '".';
			} else {
				return '请先下载模块"' . $app . '".';
			}
		}
		return true;
	}
	public function toArray() {
		$app ['name'] = $this->name;
		$app ['appname'] = $this->getName ();
		$app ['author'] = $this->getAuthor ();
		$app ['desc'] = $this->getDscription ();
		$app ['currentVer'] = $this->getCurrentVersion ();
		$app ['website'] = $this->getWebsite ();
		$app ['dependences'] = $this->getDependences ();
		return $app;
	}
	
	/**
	 * 当前版本号.
	 */
	public function getCurrentVersion() {
		$versions = $this->getVersionLists ();
		if ($versions) {
			$versions = array_keys ( $versions );
			return array_pop ( $versions );
		} else {
			return '0.0.0';
		}
	}
	/**
	 * application name.
	 *
	 * @return string
	 */
	public abstract function getName();
	
	/**
	 * application author.
	 *
	 * @return string
	 */
	public abstract function getAuthor();
	/**
	 * app home url.
	 *
	 * @return string
	 */
	public abstract function getWebsite();
	/**
	 * application description.
	 *
	 * @return string
	 */
	public abstract function getDscription();
	/**
	 * application version lists.
	 *
	 * @return array array('verion'=>'build number')
	 */
	public abstract function getVersionLists();
}