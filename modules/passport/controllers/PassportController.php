<?php

/**
 * 通行证.
 *
 * @author ngf
 */
class PassportController extends Controller {

	/**
	 * @param string $from
	 *
	 * @return \ThemeView
	 */
	public function index($from = '') {
		if (!$from) {
			$from = sess_get('passport_from');
		}
		if (!$from) {
			$from = $_SERVER['HTTP_REFERER'];
		}
		if (!$from) {
			$from = cfg('redirect_url@passport', DETECTED_ABS_URL);
		}
		$this->user = whoami('vip');
		if ($this->user->isLogin()) {
			Response::redirect($from);
		}
		$_SESSION ['passport_from'] = $from;
		$count                      = sess_get('_auth_passport_try_count', 1);

		if ($count > 3) {
			$data ['captcha'] = true;
		} else {
			$data ['captcha'] = false;
		}
		if (bcfg('enable_captcha1@passport')) {
			$data ['captcha'] = true;
		}
		$form_id                     = rand_str(10);
		$_SESSION ['_login_form_id'] = $form_id;
		$data ['_form_id']           = $form_id;
		$form                        = new \passport\forms\SignInForm();
		$data ['rules']              = $form->rules();
		$data ['enableOAuth']        = bcfg('enable_oauth@passport');
		if ($data ['enableOAuth']) {
			$data ['oauthVendors'] = \passport\classes\OauthVendorManager::getVenders();
		}
		$data ['allowJoin'] = bcfg('allow_join@passport');

		return template('passport/login.tpl', $data);
	}

	/**
	 * 登录.
	 *
	 * @param string $username
	 * @param string $passwd
	 * @param string $_form_id
	 * @param string $captcha
	 *
	 * @return string json data
	 */
	public function index_post($username, $passwd, $_form_id, $captcha = '') {
		if (!Request::isAjaxRequest()) {
			Response::respond(500);
		}
		if (empty ($_form_id) || $_form_id != sess_get('_login_form_id')) {
			Response::respond(500);
		}
		$count              = sess_get('_auth_passport_try_count', 1);
		$data ['username']  = $username;
		$data ['errorMsg']  = false;
		$data ['errorType'] = 0;
		$data ['success']   = false;
		if ($count > 3 || bcfg('enable_captcha1@passport')) {
			$auth_code_obj = new CaptchaCode ();
			if (!$auth_code_obj->validate($captcha, false)) {
				$data ['errorMsg']  = '验证码不正确.';
				$data ['errorType'] = 1;
			}
		}
		if (empty ($data ['errorMsg'])) {
			$form     = new \passport\forms\SignInForm();
			$formData = $form->valid();
			if ($formData) {
				if (strpos($username, '@') > 0) {
					$idf = 'email';
				} else {
					$idf = 'phone';
				}
				$where [ $idf ]   = $formData ['username'];
				$where['deleted'] = 0;
				$user             = dbselect('*')->from('{member}')->where($where)->get();
				if ($user) {
					if ($user ['passwd'] != MemberModelForm::generatePwd($passwd, $user['salt']) || $user [ $idf ] !== $formData ['username']) {
						$data ['errorMsg']  = __('@auth:Invalide User Name or Password.');
						$data ['errorType'] = 2;
					} else if (empty ($user ['status'])) {
						$data ['errorMsg']  = __('@auth:User is locked!');
						$data ['errorType'] = 3;
					} else if ($idf == 'email' && ($user ['status'] == '2' || $user ['status'] == '3')) {
						sess_del('_auth_passport_try_count');
						$data ['success'] = true;
						$data ['url']     = tourl('passport/active') . $user['mid'];
					} else {
						sess_del('_auth_passport_try_count');
						$this->user       = whoami('vip');
						$user ['logined'] = true;

						$metas = dbselect('name,value')->from('{member_meta}')->where(array('mid' => $user ['mid']))->toArray('value', 'name');
						if ($metas) {
							$user = array_merge($metas, $user);
						}
						$this->user->save($user);
						ActivityLog::info(__('Member %s(%s) Login successfully.', $this->user->getAccount(), $this->user->getDisplayName()), 'MLogin');
						$callback         = sess_get('passport_from', cfg('redirect_url@passport', DETECTED_ABS_URL));
						$data ['success'] = true;
						$data ['url']     = $callback;
					}
				} else {
					$data ['errorMsg']  = __('@auth:Invalide User Name or Password.');
					$data ['errorType'] = 2;
				}
			} else {
				$data ['errorMsg']  = __('@auth:Invalide User Name or Password.');
				$data ['errorType'] = 2;
			}
		}
		if (!$data['success']) {
			$count++;
			$_SESSION['_auth_passport_try_count'] = $count;
			$data['count']                        = $count;
		}

		return new JsonView($data);
	}

	/**
	 * @return \SmartyView
	 */
	public function um() {
		Response::nocache();
		$user           = whoami('vip');
		$data ['login'] = 'false';
		$data ['user']  = '{}';
		$data['sid']    = '';
		if ($user->isLogin()) {
			$data ['login'] = 'true';
			$data ['user']  = $user->serialize();
			$data['sid']    = session_id();
		}

		return view('um.tpl', $data, array('Content-Type' => 'application/javascript'));
	}

	public function logout() {
		$user = whoami('vip');
		$user->logout();
		Response::redirect(tourl('passport'));
	}
}