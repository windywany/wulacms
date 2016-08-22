<?php
class SetupForm extends AbstractForm {
	private $site_name = array ('rules' => array ('required' ) );
	private $email = array ('rules' => array ('required','email' ) );
	private $name = array ('rules' => array ('required','regexp(/^[a-z][a-z0-9_\\.]{3,14}$/i)' ) );
	private $passwd = array ('rules' => array ('required','minlength(6)','maxlength(16)' ) );
	private $passwd1 = array ('rules' => array ('equalTo(passwd)' ) );
	private $urlm = array ('rules' => array ('regexp(/^[a-z][a-z0-9_]*$/i)' ) );
}
class DatabaseForm extends AbstractForm {
	private $driver = array ('rules' => array ('required' ) );
	private $host = array ('rules' => array ('required' ) );
	private $port = array ('rules' => array ('num' ) );
	private $dbname = array ('rules' => array ('required' ) );
	private $dbuser = array ('rules' => array ('required' ) );
	private $passwd = array ('rules' => array ('required' ) );
	private $prefix = array ('rules' => array ('regexp(/^[a-z][a-z0-9]*_$/i)' => '前缀只能由字母，数字和下划线组成且以字母开头.' ) );
}
class KissgoInstaller {
	private $op, $setup, $db, $progress, $lastp;
	private $dbname;
	public function __construct($op, $setup, $db) {
		$this->op = $op;
		$this->setup = $setup;
		$this->db = $db;
		$this->progress = sess_get ( 'progress', 0 );
		$this->lastp = sess_get ( 'lastp', 100 );
		$this->dbname = $db ['dbname'];
		$this->db ['user'] = $db ['dbuser'];
		$this->db ['password'] = $db ['passwd'];
		if (empty ( $db ['port'] )) {
			unset ( $this->db ['port'] );
		}
		unset ( $this->db ['dbuser'], $this->db ['passwd'] );
		if (! sess_get ( 'createdb_done', false )) {
			unset ( $this->db ['dbname'] );
		}
	}
	public static function checkPhpExtention(&$checked) {
		$checked ['PHP'] = array ('required' => '5.6.0+','checked' => phpversion (),'pass' => version_compare ( '5.6.0', phpversion (), '<=' ) );
		$pass = extension_loaded ( 'pdo' );
		if ($pass) {
			$drivers = PDO::getAvailableDrivers ();
			if (empty ( $drivers )) {
				$pass = false;
			} else {
				$pass = in_array ( 'mysql', $drivers );
			}
		}
		$checked ['PDO (mysql)'] = array ('required' => '有','checked' => $pass ? '有' : '无','pass' => $pass );
		$pass = extension_loaded ( 'gd' );
		$checked ['GD'] = array ('required' => '有','checked' => $pass ? '有' : '无','pass' => $pass );
		$pass = extension_loaded ( 'json' );
		$checked ['JSON'] = array ('required' => '有','checked' => $pass ? '有' : '无','pass' => $pass );
		$pass = extension_loaded ( 'mbstring' );
		$checked ['MB String'] = array ('required' => '有','checked' => $pass ? '有' : '无','pass' => $pass );
		
		$pass = extension_loaded ( 'apc' );
		$checked ['apcu'] = array ('required' => '可选','checked' => $pass ? '有' : '无','pass' => $pass );
		
		$pass = extension_loaded ( 'memcache' ) || extension_loaded ( 'memcached' );
		$checked ['memcache(d)'] = array ('required' => '可选','checked' => $pass ? '有' : '无','pass' => $pass );
		
		$pass = extension_loaded ( 'curl' );
		$checked ['cURL'] = array ('required' => '可选','checked' => $pass ? '有' : '无','pass' => $pass );
		
		$pass = extension_loaded ( 'scws' );
		$checked ['scws'] = array ('required' => '可选','checked' => $pass ? '有' : '无','pass' => $pass );
	}
	public static function checkFilePermission(&$files) {
		$f = APPDATA_PATH;
		$files [$f] = AppInstaller::checkFile ( $f );
		$f = TMP_PATH;
		$files [$f] = AppInstaller::checkFile ( $f );
		$f = APPDATA_PATH . 'logs' . DS;
		$files [$f] = AppInstaller::checkFile ( $f );
		$f = APPDATA_PATH . 'default.settings.php';
		$files [$f] = AppInstaller::checkFile ( $f, true, false );
	}
	public function install() {
		if (method_exists ( $this, $this->op )) {
			return call_user_func_array ( array ($this,$this->op ), array () );
		} else {
			$this->error ( '未知的安装步骤:' . $this->op );
			return array ('success' => false,'msg' => '未知的安装步骤:' . $this->op );
		}
	}
	private function init() {
		$cnt = 4;
		$apps = KissGoSetting::getBuiltInApps ();
		$_SESSION ['BIAPPS'] = array ();
		$_SESSION ['createdb_done'] = false;
		$_SESSION ['install_done'] = true;
		$_SESSION ['msg'] = '';
		if ($apps) {
			foreach ( $apps as $app ) {
				if (AppInstaller::hasInstaller ( $app )) {
					$cnt ++;
					$_SESSION ['BIAPPS'] [] = $app;
				}
			}
		}
		$progress = floor ( 100 / $cnt );
		$lastp = 100 - $progress * ($cnt - 1);
		$_SESSION ['progress'] = $progress;
		$_SESSION ['lastp'] = $lastp;
		$data = array ('success' => true,'progres' => $progress,'next' => 'createdb','name' => '创建数据库' );
		return $data;
	}
	private function createdb() {
		$data = array ('success' => false,'msg' => '无法连接数据库。' );
		$dialect = DatabaseDialect::getDialect ( $this->db );
		if ($dialect) {
			$dbs = $dialect->listDatabases ();
			$rst = in_array ( $this->dbname, $dbs );
			if (! $rst) {
				$rst = $dialect->createDatabase ( $this->dbname, 'UTF8' );
			}
			if ($rst) {
				$data = $this->nextAppStep ();
				$_SESSION ['createdb_done'] = true;
			} else {
				$data ['msg'] = DatabaseDialect::$lastErrorMassge;
			}
		}
		if (isset ( $data ['msg'] )) {
			$this->error ( $data ['msg'] );
		}
		return $data;
	}
	private function createadmin() {
		$data = array ('success' => false,'msg' => '无法连接数据库。' );
		$dialect = DatabaseDialect::getDialect ( $this->db );
		if ($dialect) {
			$admin = array ('user_id' => 1,'group_id' => 1,'username' => $this->setup ['name'],'email' => $this->setup ['email'],'passwd' => md5 ( $this->setup ['passwd'] ),'status' => 1,'registered' => time (),'update_time' => time (),'group_id' => 0,'nickname' => '超级管理员','ip' => $_SERVER ['REMOTE_ADDR'] );
			$adminSQL = dbinsert ( $admin )->into ( '{user}' );
			$adminSQL->setDialect ( $dialect );
			$userRoleSQL = dbinsert ( array ('user_id' => 1,'role_id' => 1,'sort' => 0 ) )->into ( '{user_has_role}' );
			$userRoleSQL->setDialect ( $dialect );
			if (execSQL ( $adminSQL, $userRoleSQL )) {
				$data = array ('success' => true,'progres' => $this->progress,'next' => 'savecnf','name' => '创建配置文件' );
			} else {
				$data ['msg'] = DatabaseDialect::$lastErrorMassge;
			}
		}
		if (isset ( $data ['msg'] )) {
			$this->error ( $data ['msg'] );
		}
		return $data;
	}
	private function savecnf() {
		$settings = KissGoSetting::getSetting ( 'install' );
		$settings ['DEBUG'] = DEBUG_INFO;
		$settings ['CLEAN_URL'] = $this->setup ['clean_url'] ? true : false;
		$settings ['I18N_ENABLED'] = true;
		$settings ['GZIP_ENABLED'] = $this->setup ['gzip'] ? true : false;
		$settings ['TIMEZONE'] = 'Asia/Shanghai';
		$settings ['SECURITY_KEY'] = rand_str ( 16 );
		$this->db ['encoding'] = 'UTF8';
		$settings ['database'] = array ('default' => $this->db );
		$rst = $settings->saveSettingToFile ( APPDATA_PATH . 'settings.php' );
		if ($rst === true) {
			$dialect = DatabaseDialect::getDialect ( $this->db );
			$pSQL = dbinsert ( array ('user_id' => 1,'update_time' => time (),'preference_group' => 'core','name' => 'site_name','value' => $this->setup ['site_name'] ) )->into ( '{preferences}' );
			$pSQL->setDialect ( $dialect );
			$pSQL->exec ();
			$data = array ('success' => true,'progres' => $this->lastp,'next' => 'done','name' => '' );
		} else {
			$this->error ( $rst );
			$data = array ('success' => false,'msg' => $rst );
		}
		return $data;
	}
	private function installapp() {
		$app = rqst ( 'app' );
		$installer = AppInstaller::getAppInstaller ( $app );
		$dialect = DatabaseDialect::getDialect ( $this->db );
		if ($installer && $dialect) {
			// 安装操作
			$clz_dir = MODULES_PATH . $app . DS . 'classes';
			if (is_dir ( $clz_dir )) {
				$__kissgo_exports [] = $clz_dir;
			}
			$dialect->beginTransaction ();
			$sqls = $installer->getSqls ( $dialect->getDriverName (), $installer->getCurrentVersion () );
			$rst = AppInstaller::execSql ( $dialect, $sqls );
			if ($rst === true) {
				$appdata = array ('user_id' => 1,'update_time' => time (),'app' => $app,'status' => 1,'system' => 1,'urlmapping' => $app,'version' => $installer->getCurrentVersion () );
				if ($app == 'dashboard') {
					$urlm = $this->setup ['urlm'];
					if (! empty ( $urlm ) && $urlm != 'dashboard') {
						$appdata ['urlmapping'] = $urlm;
					}
				}
				$sql = dbinsert ( $appdata )->into ( '{apps}' )->usedb ( $dialect );
				$rst = $sql->exec ();
				if ($rst) {
					$rst = $installer->onInstall ( $dialect );
				}
			} else {
				$installer->setLastError ( $rst );
				$rst = false;
			}
			if (! $rst) {
				$dialect->rollBack ();
				$this->error ( '安装应用出错啦：' . $installer->getLastError () );
				$data = array ('success' => false,'msg' => '安装应用出错啦：' . $installer->getLastError () );
			} else {
				$dialect->commit ();
				$data = $this->nextAppStep ();
			}
		} else if (! $installer) {
			$this->error ( '未知应用：' . $app );
			$data = array ('success' => false,'msg' => '未知应用：' . $app );
		} else {
			$this->error ( '无法连接数据库：' . DatabaseDialect::$lastErrorMassge );
			$data = array ('success' => false,'msg' => '无法连接数据库：' . DatabaseDialect::$lastErrorMassge );
		}
		return $data;
	}
	private function getNextApp() {
		$app = null;
		$apps = sess_get ( 'BIAPPS', array () );
		if ($apps) {
			$app = array_shift ( $apps );
			$installer = AppInstaller::getAppInstaller ( $app );
			$_SESSION ['BIAPPS'] = $apps;
		}
		return $app;
	}
	private function nextAppStep() {
		$app = $this->getNextApp ();
		if ($app) {
			$installer = AppInstaller::getAppInstaller ( $app );
			$data = array ('success' => true,'progres' => $this->progress,'next' => 'installapp','data' => array ('app' => $app ),'name' => '安装程序 - ' . $installer->getName () );
		} else {
			$data = array ('success' => true,'progres' => $this->progress,'next' => 'createadmin','name' => '创建超级管理员' );
		}
		return $data;
	}
	private function error($messge) {
		$_SESSION ['install_done'] = 'error';
		$_SESSION ['msg'] = $messge;
	}
}