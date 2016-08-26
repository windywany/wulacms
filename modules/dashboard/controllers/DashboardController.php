<?php
/**
 * 管理员界面控制器.
 *
 * @author Leo Ning <windywany@gmail.com>
 */
class DashboardController extends Controller {
	public function preRun($method) {
		$dhost = cfg ( 'site_url' );
		if ($dhost) {
			$dhost = parse_url ( $dhost, PHP_URL_HOST );
			if ($dhost) {
				$host = REAL_HTTP_HOST;
				if ($dhost != $host) {
					Response::respond ( 404 );
				}
			}
		}
		parent::preRun ( $method );
	}
	/**
	 * 管理后台首页.
	 *
	 * @return SmartyView
	 */
	public function index() {
		if ($this->user->isLogin ()) {
			$data = array ('passport' => $this->user );
			$data ['cp_theme'] = $this->user->getAttr ( 'theme', '2' );
			$data ['cp_theme'] = 'smart-style-' . $data ['cp_theme'];
			$data ['menu_on_top'] = $this->user->getAttr ( 'menu_on_top', 1 );
			if ($data ['menu_on_top']) {
				$data ['menu_fixed'] = $this->user->getAttr ( 'menu_fixed', 1 );
			} else {
				$data ['menu_fixed'] = 0;
			}
			$data = apply_filter ( 'on_init_layout_data', $data );
			$data['isOffline'] = bcfg('isOffline1');
			$layoutManager = new AdminLayoutManager ();
			fire ( 'do_admin_layout', $layoutManager );
			$data ['layoutManager'] = $layoutManager;
			$tpl = 'index.tpl';
		} else if (bcfg ( 'enable_client@corepst' ) && preg_match ( '#^http.*#', cfg ( 'site_url' ) )) {
			$callback = tourl ( 'dashboard/login' );
			Response::redirect ( cfg ( 'login_url@corepst' ), array ('from' => $callback,'appid' => cfg ( 'appkey@corepst' ) ) );
		} else {
			$count = sess_get ( '_auth_try_count', 1 );
			if ($count > 3) {
				$data ['captcha'] = true;
			} else {
				$data ['captcha'] = false;
			}
			$form = new AuthForm ();
			$data ['rules'] = $form->rules ();
			$tpl = 'login.tpl';
		}
		$tpl = apply_filter ( 'get_dashboard_login_tpl', $tpl );
		return view ( $tpl, $data );
	}
	/**
	 * check user login.
	 *
	 * @param string $username        	
	 * @param string $passwd        	
	 * @param string $captcha        	
	 * @return SmartyView
	 */
	public function index_post($username, $passwd, $captcha = '') {
		$count = sess_get ( '_auth_try_count', 1 );
		$data ['username'] = $username;
		$data ['errorMsg'] = false;
		if ($count > 3) {
			$auth_code_obj = new CaptchaCode ();
			if (! $auth_code_obj->validate ( $captcha, false )) {
				$data ['errorMsg'] = '验证码不正确.';
			}
		}
		if (empty ( $data ['errorMsg'] )) {
			$form = new AuthForm ();
			$formData = $form->valid ();
			if ($formData) {
				if (strpos ( $formData ['username'], '@' )) {
					$where ['email'] = $formData ['username'];
					$id = 'email';
				} else {
					$where ['username'] = $formData ['username'];
					$id = 'username';
				}
				$user = dbselect ( '*' )->from ( '{user}' )->where ( $where );
				if (count ( $user ) == 0 || $user [0] ['passwd'] != md5 ( $formData ['passwd'] ) || $user [0] [$id] != $formData ['username']) {
					$data ['errorMsg'] = __ ( '@auth:Invalide User Name or Password.' );
				} else if (empty ( $user [0] ['status'] )) {
					$data ['errorMsg'] = __ ( '@auth:User is locked!' );
				} else {
					$user = $user [0];
					$user ['logined'] = true;
					$this->user->save ( $user );
					sess_del ( '_auth_try_count' );
				}
			} else {
				$data ['errorMsg'] = __ ( '@auth:Invalide User Name or Password.' );
			}
		}
		
		if (empty ( $data ['errorMsg'] )) {
			ActivityLog::info ( __ ( '%s(%s) Login successfully.', $this->user->getAccount (), $this->user->getDisplayName () ), 'Login' );
			$to = sess_del ( 'DASHBOARD_REDIRECT_TO' );
			Response::redirect ( tourl ( 'dashboard' ) );
		} else {
			$_SESSION ['_auth_try_count'] = ++ $count;
			if ($count > 3) {
				$data ['captcha'] = true;
			}
			return view ( 'login.tpl', $data );
		}
	}
	public function login($token) {
		$appsecret = cfg ( 'appsecret@corepst' );
		$token = authcode ( $token, 'DECODE', $appsecret, 300 );
		if ($token) {
			$url = cfg ( 'url@corepst' );
			$appid = cfg ( 'appkey@corepst' );
			$client = new RestClient ( $url, $appid, $appsecret );
			$user = $client->get ( 'passport.admin.userinfo', array ('token' => $token ) );
			if (isset ( $user ['user'] )) {
				$user = $user ['user'];
				$this->updateUserInfo ( $user );
				$user ['logined'] = true;
				$this->user->save ( $user );
				ActivityLog::info ( __ ( '%s(%s) Login successfully (Passport).', $this->user->getAccount (), $this->user->getDisplayName () ), 'Login' );
				Response::redirect ( tourl ( 'dashboard' ) );
			} else if (isset ( $user ['message'] )) {
				return $user ['message'];
			}
		}
		return 'login error!';
	}
	/**
	 * logout.
	 */
	public function signout() {
		ActivityLog::info ( __ ( '%s(%s) Logout successfully.', $this->user->getAccount (), $this->user->getDisplayName () ), 'Logout' );
		$this->user->logout ();
		sess_del ( '_auth_try_count' );
		Response::redirect ( tourl ( 'dashboard' ) );
	}
	/**
	 * 控制面板首页.
	 *
	 * @return SmartyView
	 */
	public function cp() {
		if ($this->user->isLogin ()) {
			$data ['noticeClosed'] = sess_get ( 'noticeClosed', false );
			if (! $data ['noticeClosed']) {
				$notice = dbselect ( 'title,message,NT.create_time,U.nickname' )->from ( '{notification} AS NT' )->where ( array ('NT.deleted' => 0,'expire_time >' => time () ) )->desc ( 'id' );
				$notice->join ( '{user} AS U', 'U.user_id = NT.create_uid' );
				$notice = $notice->get ( 0 );
				if ($notice) {
					$data ['noticeTitle'] = $notice ['title'];
					$data ['noticeMessage'] = $notice ['message'];
					$data ['noticeUser'] = $notice ['nickname'];
					$data ['noticeTime'] = date ( 'Y-m-d', $notice ['create_time'] );
				} else {
					$data ['noticeClosed'] = true;
				}
			}
			$uiManager = apply_filter ( 'on_init_dashboard_ui', new DashboardUIManager () );
			$data ['dashboardUI'] = $uiManager;
			$system = AppInstaller::getAppInstaller ( 'system' );
			$data ['kernelVer'] = $system->getInstalledVersion ();
			$dcp = AppInstaller::getAppInstaller ( 'dashboard' );
			$data ['cpVer'] = $dcp->getInstalledVersion ();
			$phpInfo [] = "PHP " . phpversion ();
			$dialect = DatabaseDialect::getDialect ();
			$phpInfo [] = "PDO " . $dialect->getDriverName () . ' - ' . $dialect->getAttribute ( PDO::ATTR_CLIENT_VERSION );
			$data ['phpInfo'] = implode ( ', ', $phpInfo );
			$data ['serverName'] = $_SERVER ['SERVER_SOFTWARE'];
			$data ['dbInfo'] = $dialect->getDriverName () . ' - ' . $dialect->getAttribute ( PDO::ATTR_SERVER_VERSION );
			if (extension_loaded ( 'scws' )) {
				$scws = scws_new ();
				$data ['scwsInfo'] = 'scws ' . scws_version ();
				$scws->close ();
			} else {
				$data ['scwsInfo'] = '无';
			}
			$data ['rtcacheInfo'] = RtCache::getInfo ();
			$gd = gd_info ();
			$data ['gdInfo'] = $gd ['GD Version'];
			$data ['sessionManager'] = ini_get ( 'session.save_handler' ) . '&nbsp;[' . ini_get ( 'session.save_path' ) . ']';
			$data ['devMod'] = bcfg ( 'develop_mode' ) ? '开启' : '关闭';
			$logs = array (DEBUG_INFO => 'INFO',DEBUG_WARN => 'WARN',DEBUG_DEBUG => 'DEBUG',DEBUG_ERROR => 'ERROR',DEBUG_OFF => 'OFF' );
			$data ['logLevel'] = $logs [DEBUG];
			return view ( 'dashboard.tpl', $data );
		} else {
			Response::redirect ( tourl ( 'dashboard' ) );
		}
	}
	/**
	 * 关闭通知.
	 *
	 * @return NuiAjaxView.
	 */
	public function closenotice() {
		$_SESSION ['noticeClosed'] = true;
		return NuiAjaxView::ok ();
	}
	private function updateUserInfo($user) {
		$uid = $user ['user_id'];
		$where ['user_id'] = $uid;
		if (dbselect ()->from ( '{user}' )->where ( $where )->exist ( 'user_id' )) {
			dbupdate ( '{user}' )->set ( $user )->where ( $where )->exec ();
		} else {
			$user ['passwd'] = md5 ( rand_str ( 10 ) );
			$user ['group_id'] = 0;
			dbinsert ( $user )->into ( '{user}' )->exec ();
		}
	}
}