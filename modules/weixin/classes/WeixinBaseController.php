<?php
/**
 * 需要微信静默登录认证的页面.
 * @author leo
 *
 */
abstract class WeixinBaseController extends Controller {
	protected $openid = NULL;
	protected $exludes = false;
	const SCOPE_SNSAPI_BASE = 'snsapi_base';
	const SCOPE_SNSAPI_USERINFO = 'snsapi_userinfo';
	public function preRun($method) {
		$user = whoami ( 'weixin' );
		if (! $user->isLogin ()) {
			$AppID = cfg ( 'LoginAppID@weixin' );
			$AppSecret = cfg ( 'LoginAppSecret@weixin' );
			if (! $AppID || ! $AppSecret) {
				Response::respond ( 500, 'weixin configuration error.' );
			}
			$code = rqst ( 'code' );
			$state = rqst ( 'state' );
			if ($state) {
				$rstate = sess_get ( 'weixin_state' );
				if ($state == $rstate) {
					if ($code) {
						$this->checkMpOAuth ( $AppID, $AppSecret, $code );
					} else {
						// 用户取消登录.
						Response::showErrorMsg ( '用户取消登录.', 500 );
					}
				}else{
					log_error('crsf tack:'.Request::getInstance ( true )->getUri (),'weixin');
				}
			}
			$curl = Request::getInstance ( true )->getUri ();
			//url中
			$curl = preg_replace('#(state|code)=[^&]*&?#', '', $curl);
			$current_page = urlencode ( $curl );
			$state = rand_str ( 8 );
			$_SESSION ['weixin_state'] = $state;
			$scope = $this->getOAuthScope ();
			$oauth_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$AppID}&redirect_uri={$current_page}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
			$this->checkUser = array ($oauth_url,'weixin' );
			parent::preRun ( $method );
		} else {
			$this->user = $user;
			$this->_check_acls ( $method );
			if (! is_array ( $this->exludes ) || empty ( $this->exludes ) || ! in_array ( $method, $this->exludes )) {
				$unid = $this->user->getAttr ( 'unionid' );
				if ($unid) {
					if (! dbselect ( '*' )->from ( '{weixin_subscriber}' )->where ( array ('unionid' => $unid,'subscribe' => 1 ) )->exist ( 'id' )) {
						Response::redirect ( tourl ( 'weixin/sub' ) );
					}
				}
			}
		}
	}
	protected function getOAuthScope() {
		return bcfg ( 'GetUserInfo@weixin' ) ? WeixinBaseController::SCOPE_SNSAPI_USERINFO : WeixinBaseController::SCOPE_SNSAPI_BASE;
	}
	protected function checkMpOAuth($AppID, $AppSecret, $code) {
		// 微信回调成功,完成登录操作.
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$AppID}&secret={$AppSecret}&code={$code}&grant_type=authorization_code";
		$rtn = WeixinUtil::apiGet ( $url );
		if ($rtn ['openid']) {
			$user ['openid'] = $rtn ['openid'];
			$user ['unionid'] = $rtn ['unionid'];
			$oldinfo = dbselect ( 'update_time,unionid' )->from ( '{weixin_user}' )->where ( $user )->get ( 0 );
			$curtime = time ();
			$user ['access_token'] = $rtn ['access_token'];
			$user ['access_token_expire'] = $curtime + $rtn ['expires_in'];
			$user ['refresh_token'] = $rtn ['refresh_token'];
			$user ['refresh_token_expire'] = $curtime + 604800;
			
			if (! $oldinfo) {
				$user ['create_time'] = time ();
				$user ['update_time'] = time ();
				dbinsert ( $user )->into ( '{weixin_user}' )->exec ();
			} else {
				$user ['update_time'] = $oldinfo ['update_time'];
				dbupdate ( '{weixin_user}' )->set ( $user )->where ( array ('openid' => $user ['openid'],'unionid' => $oldinfo ['unionid'] ) )->exec ();
			}
			
			$scope = $rtn ['scope'];
			$access_token = $rtn ['access_token'];
			$passort = Passport::getPassport ( 0, 'weixin' );
			$passort->setAccount ( $user ['openid'] );
			$passort->setAttr ( 'unionid', $user ['unionid'] );
			$passort->isLogin ( true );
			fire ( 'on_weixin_user_login', $passort );
			$info = array ();
			if (bcfg ( 'GetUserInfo@weixin' )) {
				$interval = icfg ( 'UpdateInfo@weixin', 1 );
				// 2015-12-14 00:00:00 +1 days
				$expire = strtotime ( date ( 'Y-m-d 00:00:00', $user ['update_time'] ) . ($interval >= 0 ? ' +' : ' -') . abs ( $interval ) . ' days' );
				if ($interval >= 0 && $expire < time ()) {
					if (strpos ( $rtn ['scope'], WeixinBaseController::SCOPE_SNSAPI_USERINFO ) !== false || strpos ( $rtn ['scope'], WeixinBaseController::SCOPE_SNSAPI_LOGIN ) !== false) {
						// 取微信用户信息.
						$url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$user['openid']}&lang=zh_CN";
						$info = WeixinUtil::apiGet ( $url, 0, 'weixin_response_filter' );
						if ($info) {
							$info = apply_filter ( 'update_weixin_user_info', $info );
							if ($info) {
								$data ['update_time'] = time ();
								dbupdate ( '{weixin_user}' )->set ( $data )->where ( array ('openid' => $user ['openid'] ) )->exec ();
							}
						} else {
							log_warn ( '[weixin] Cannot get the info of user!','weixin' );
						}
					}
				}
			}
			$passort->setAttr ( 'weixin_info', $info );
			$passort->save ();
		} else {
			log_warn ( '[weixin] Cannot get the openid of user!','weixin'  );
			// 拿不到用户取消登录.
			Response::showErrorMsg ( '用户取消登录，拿不到用户信息.', 500 );
		}
	}
}
