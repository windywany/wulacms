<?php
/**
 * 通行证.
 *
 * @author ngf
 */
class PassportController extends AbstractPassportController {
	protected $checkUser = null;
	public function preRun($method) {
		if (! bcfg ( 'allow_remote@passport' ) && $method != 'logout' && $method != 'login' && $method != 'um') {
			$type = cfg ( 'type@passport', 'vip' );
			$this->user = whoami ( $type );
			if ($this->user->isLogin ()) {
				if ($type == 'admin') {
					Response::redirect ( tourl ( 'dashboard' ) );
				} else {
					$callback = cfg ( 'redirect_url@passport', DETECTED_ABS_URL );
					Response::redirect ( $callback );
				}
			}
		}
		$this->setTheme ( false );
	}
	/**
	 * 登录页.
	 *
	 * @param unknown $from
	 * @param unknown $appid
	 * @return SmartyView
	 */
	public function index($from = '', $appid = '', $um = '') {
		if (bcfg ( 'connect_to@passport' )) {
			// 接入通行证服务器.
			$appid = cfg ( 'appkey@rest' );
			$passport_url = cfg ( 'passport_url@passport' );
			$um = rand_str ( 5 );
			$_SESSION ['passport_my_um'] = $um;
			if (! $from) {
				$form = $_SERVER ['HTTP_REFERER'];
			}
			Response::redirect ( url_append_args ( $passport_url, array ('appid' => $appid,'from' => $from,'um' => $um ) ) );
		}
		if ($from) {
			$_SESSION ['passport_rt2'] = $from;
		}
		if ($appid) {
			$_SESSION ['passport_appid'] = $appid;
		}
		if ($um) {
			$_SESSION ['passport_c_um'] = $um;
		}
		$count = sess_get ( '_auth_passport_try_count', 1 );
		if ($count > 3) {
			$data ['captcha'] = true;
		} else {
			$data ['captcha'] = false;
		}
		$form_id = rand_str ( 10 );
		$_SESSION ['_login_form_id'] = $form_id;
		$data ['_form_id'] = $form_id;
		$form = new AuthForm ();
		$data ['rules'] = $form->rules ();
		$data ['enableOAuth'] = bcfg ( 'enable_oauth@passport' );
		if ($data ['enableOAuth']) {
			$data ['oauthVendors'] = PassportPluginImpl::getOauthVendors ();
		}
		$data ['allowJoin'] = bcfg ( 'allow_join@passport' );
		$data ['errorMsg'] = sess_del ( '_auth_passport_error' );
		$data ['username'] = sess_get ( '_auth_username', '' );
		$data ['passport_type'] = cfg ( 'type@passport', 'vip' );
		return view ( $this->theme->login (), $data );
	}
	/**
	 * 提交处理。
	 */
	public function index_post($username, $passwd, $_form_id, $captcha = '') {
		if (bcfg ( 'connect_to@passport' )) {
			// 接入通行证服务器后登录不可提交到此页面.
			Response::respond ( 404 );
		}
		if (empty ( $_form_id ) || $_form_id != sess_get ( '_login_form_id' )) {
			Response::respond ( 404 );
		}
		$count = sess_get ( '_auth_passport_try_count', 1 );
		$data ['username'] = $username;
		$data ['errorMsg'] = false;
		if ($count > 3) {
			$auth_code_obj = new CaptchaCode ();
			if (! $auth_code_obj->validate ( $captcha, false )) {
				$data ['errorMsg'] = '验证码不正确.';
			}
		}
		$session_id = uniqid ();
		$account = false;
		if (empty ( $data ['errorMsg'] )) {
			$form = new AuthForm ();
			$formData = $form->valid ();
			if ($formData) {
				$type = cfg ( 'type@passport', 'vip' );
				$allow_remote = bcfg ( 'allow_remote@passport' );
				if (strpos ( $formData ['username'], '@' )) {
					$where ['email'] = $formData ['username'];
					$id = 'email';
				} else {
					$where ['username'] = $formData ['username'];
					$id = 'username';
				}
				if ($type == 'admin') {
					$user = dbselect ( '*' )->from ( '{user}' )->where ( $where );
					$idf = 'user_id';
				} else {
					$user = dbselect ( '*' )->from ( '{member}' )->where ( $where );
					$idf = 'mid';
				}
				if (count ( $user ) == 0 || $user [0] ['passwd'] != md5 ( $formData ['passwd'] ) || $user [0] [$id] != $formData ['username']) {
					$data ['errorMsg'] = __ ( '@auth:Invalide User Name or Password.' );
				} else if (empty ( $user [0] ['status'] )) {
					$data ['errorMsg'] = __ ( '@auth:User is locked!' );
				} else if ($user [0] ['status'] == '2' || $user [0] ['status'] == '3') {
					sess_del ( '_auth_passport_try_count' );
					sess_del ( '_auth_passport_error' );
					sess_del ( '_auth_username' );
					Response::redirect ( tourl ( 'passport/active' ) . $user [0] [$idf] );
				} else {
					if ($allow_remote) {
						// 做为通行证服务器.
						$account = $user = $user [0];
						$time = time ();
						$sess ['create_time'] = $time;
						$sess ['expire_time'] = $time + 300;
						$sess ['user_id'] = $user [$idf];
						$sess ['session_id'] = $session_id;
						if (! dbinsert ( $sess )->into ( '{passport_session}' )->exec ()) {
							$data ['errorMsg'] = __ ( '@auth:Internal Error!' );
						}
					} else {
						sess_del ( '_auth_passport_try_count' );
						sess_del ( '_auth_passport_error' );
						sess_del ( '_auth_username' );
						$this->user = whoami ( $type );
						$user = $user [0];
						$user ['logined'] = true;
						
						if ($type == 'vip') {
							$metas = dbselect ( 'name,value' )->from ( '{member_meta}' )->where ( array ('mid' => $user ['mid'] ) )->toArray ( 'value', 'name' );
							if ($metas) {
								$user = array_merge($metas,$user);
							}
						}
						$this->user->save ( $user );
						ActivityLog::info ( __ ( '%s(%s) Login successfully.', $this->user->getAccount (), $this->user->getDisplayName () ), 'Login' );
						if ($type == 'admin') {
							Response::redirect ( tourl ( 'dashboard' ) );
						} else {
							$callback = sess_get ( 'passport_rt2', cfg ( 'redirect_url@passport', DETECTED_ABS_URL ) );
							Response::redirect ( $callback );
						}
					}
				}
			} else {
				$data ['errorMsg'] = __ ( '@auth:Invalide User Name or Password.' );
			}
		}
		
		if (empty ( $data ['errorMsg'] )) {
			sess_del ( '_auth_passport_try_count' );
			sess_del ( '_auth_passport_error' );
			sess_del ( '_auth_username' );
			$callback = sess_get ( 'passport_rt2' );
			$appid = sess_get ( 'passport_appid' );
			if ($appid) {
				if (bcfg ( 'connect_server@rest' )) {
					$client = new RestClient ( cfg ( 'url@rest' ), cfg ( 'appkey@rest' ), cfg ( 'appsecret@rest' ) );
					$app = $client->get ( 'rest.get_app', array ('appID' => $appid ) );
					$client->close ();
				} else {
					$app = dbselect ( 'appsecret,callback_url' )->from ( '{rest_apps}' )->where ( array ('appkey' => $appid ) )->get ( 0 );
				}
				
				if ($app && isset ( $app ['appsecret'] )) {
					$appsecret = $app ['appsecret'];
					$rest_url = $app ['callback_url'];
				} else {
					return $this->back2login ( $data ['username'], $count, __ ( 'Cannot back to the dest website' ) );
				}
				
				if (! $rest_url) {
					return $this->back2login ( $data ['username'], $count, __ ( 'Cannot back to the dest website' ) );
				}
				
				$client = new RestClient ( $rest_url, cfg ( 'appkey@rest' ), cfg ( 'appsecret@rest' ) );
				$redirect_to = $client->get ( 'passport.member.callback', array () );
				$client->close ();
				if ($redirect_to && isset ( $redirect_to ['url'] )) {
					$session_id = authcode ( $session_id, 'ENCODE', $appsecret, 300 );
					ActivityLog::info ( __ ( '%s(%s) Login successfully (%s).', $account ['username'], $account ['nickname'], $callback ), 'Login' );
					sess_del ( 'passport_rt2' );
					sess_del ( 'passport_appid' );
					$um = sess_del ( 'passport_c_um' );
					Response::redirect ( url_append_args ( $redirect_to ['url'], array ('token' => $session_id,'from' => $callback,'um' => $um ) ) );
				} else {
					$data ['errorMsg'] = __ ( 'Cannot back to the dest website' );
				}
			} else {
				$data ['errorMsg'] = __ ( 'Cannot back to the dest website' );
			}
		}
		return $this->back2login ( $data ['username'], $count, $data ['errorMsg'] );
	}
	public function um() {
		$type = cfg ( 'type@passport', 'vip' );
		if ($type == 'admin') {
			// 管理员类型的通行证或未接入远征通行证时
			Response::respond ( 404 );
		}
		$user = whoami ( 'vip' );
		$data ['login'] = 'false';
		$data ['user'] = '{}';
		if ($user->isLogin ()) {
			$data ['login'] = 'true';
			$data ['user'] = $user->serialize ();
		}
		return view ( 'um.tpl', $data, array ('Content-Type' => 'application/javascript' ) );
	}
	/**
	 * 登录或登录回调.
	 *
	 * @param string $token
	 * @param string $from
	 */
	public function login($token = '', $from = '', $um = '') {
		$type = cfg ( 'type@passport', 'vip' );
		if ($type == 'admin' || ! bcfg ( 'connect_to@passport' )) {
			// 管理员类型的通行证或未接入远征通行证时
			Response::respond ( 404 );
		}
		$my_um = $_SESSION ['passport_my_um'];
		if (empty ( $um ) || $my_um != $um) {
			return "my {$my_um} you {$um}";
		}
		$user = whoami ( $type );
		$user->logout ();
		if ($token) {
			$appsecret = cfg ( 'appsecret@rest' );
			$appid = cfg ( 'appkey@rest' );
			$url = cfg ( 'passport_rest_url@passport' );
			$token = authcode ( $token, 'DECODE', $appsecret, 300 );
			if ($token && $url && $appid && $appsecret) {
				$client = new RestClient ( $url, $appid, $appsecret );
				$user = $client->get ( 'passport.member.userinfo', array ('token' => $token ) );
				if (isset ( $user ['member'] )) {
					$this->user = whoami ( 'vip' );
					$user = $user ['member'];
					$user ['logined'] = true;
					$this->user->save ( $user );
					if (bcfg ( 'sync_member@passport' )) {
						$this->syncMember ( $user );
					}
					if ($from) {
						Response::redirect ( $from );
					} else {
						Response::redirect ( cfg ( 'redirect_url@passport', DETECTED_ABS_URL ) );
					}
				} else {
					Response::showErrorMsg ( 'Login Failed:' . $user ['message'] );
				}
			}
		}
		Response::respond ( 404 );
	}
	public function logout() {
		$type = cfg ( 'type@passport', 'vip' );
		$user = whoami ( $type );
		$user->logout ();
		Response::redirect ( DETECTED_ABS_URL );
	}
	private function back2login($username, $count, $message = false) {
		$count ++;
		$_SESSION ['_auth_passport_try_count'] = $count;
		$_SESSION ['_auth_passport_error'] = $message;
		$_SESSION ['_auth_username'] = $username;
		Response::redirect ( tourl ( 'passport' ) );
	}
	private function syncMember($user) {
		$data ['avatar'] = $user ['avatar'];
		$data ['avatar_big'] = $user ['avatar_big'];
		$data ['avatar_small'] = $user ['avatar_small'];
		$data ['nickname'] = $user ['nickname'];
		$data ['username'] = $user ['username'];
		$data ['email'] = $user ['email'];
		$data ['phone'] = $user ['phone'];
		$data ['status'] = $user ['status'];
		$data ['update_time'] = time ();
		$data ['update_uid'] = 0;
		$data ['recommend_code'] = $user ['recommend_code'];
		if (dbselect ()->from ( '{member}' )->where ( array ('mid' => $user ['mid'] ) )->exist ( 'mid' )) {
			dbupdate ( '{member}' )->set ( $data )->where ( array ('mid' => $user ['mid'] ) )->exec ();
		} else {
			$data ['mid'] = $user ['mid'];
			$data ['ip'] = $user ['ip'];
			$data ['registered'] = $user ['registered'];
			$data ['type'] = $user ['type'];
			$data ['invite_mid'] = $user ['invite_mid'];
			$data ['invite_code'] = $user ['invite_code'];
			$data ['group_id'] = icfg ( 'default_group@passport' );
			$data ['role_id'] = icfg ( 'default_role@passport' );
			dbinsert ( $data )->into ( '{member}' )->exec ();
		}
	}
}