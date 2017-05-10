<?php

/**
 * 用户注册.
 * @author Guangfeng
 *
 */
class JoinController extends Controller {
	/**
	 * 注册页面.
	 *
	 * @param string $type
	 * @param string $rc
	 *            推荐码(邀请码)
	 *
	 * @return \ThemeView
	 */
	public function index($type = '', $rc = '') {
		if (!bcfg('allow_join@passport')) {
			Response::respond(404);
		}
		$this->user = whoami('vip');
		if ($this->user->isLogin()) {
			Response::redirect(tourl('passport'));
		}
		if ($rc) {
			$_SESSION['join_recommend_code'] = $rc;
			Response::cookie('_utm_rc', $rc);
		}
		$type = in_array($type, ['mail', 'phone']) ? $type : cfg('join_type@passport', 'mail');
		if ($type == 'mail') {
			$model = new \passport\models\MailSignUpModel();
		} else {
			$model = new \passport\models\PhoneSignUpModel();
		}
		$form                       = $model->getForm();
		$cform                      = apply_filter('get_custom_join_form', null);
		$data ['rules']             = $form->rules($cform);
		$data ['captcha']           = bcfg('enable_captcha@passport', true);
		$data ['agreement']         = cfg('agree@passport');
		$data ['rc']                = $rc;
		$form_id                    = rand_str(10);
		$_SESSION ['_join_form_id'] = $form_id;
		$data ['_form_id']          = $form_id;
		$data ['enableOAuth']       = bcfg('enable_oauth@passport');
		if ($data['enableOAuth']) {
			$data['oauthVendors'] = \passport\classes\OauthVendorManager::getVenders();
		}
		$data ['type']           = $type;
		$data ['enableInvation'] = bcfg('enable_invation@passport');
		$data ['inviteRequired'] = $data ['enableInvation'] && bcfg('invite_required@passport') ? true : false;
		if ($type == 'phone') {
			return template('passport/join_phone.tpl', $data);
		} else {
			return template('passport/join_mail.tpl', $data);
		}
	}

	/**
	 * 注册提交处理器.
	 *
	 * @param string $type
	 * @param string $_form_id
	 * @param string $captcha 图片验证码
	 * @param string $vcode   手机验证码
	 *
	 * @return JsonView
	 */
	public function index_post($type, $_form_id, $captcha = '', $vcode = '') {
		if (empty ($_form_id) || $_form_id != sess_get('_join_form_id')) {
			Response::respond(403);
		}
		$type             = in_array($type, ['mail', 'phone']) ? $type : cfg('join_type@passport', 'mail');
		$data['errorMsg'] = '';
		$data['success']  = false;

		if ($type == 'mail') {
			$model = new \passport\models\MailSignUpModel();
		} else {
			$model = new \passport\models\PhoneSignUpModel();
		}
		$form = $model->getForm();
		if (bcfg('enable_captcha@passport', true)) {
			$auth_code_obj = new CaptchaCode ();
			if (!$auth_code_obj->validate($captcha, false)) {
				$data ['errorMsg']  = '验证码不正确.';
				$data ['errorType'] = 1;
			}
		}
		if ($type == 'phone') {
			$rst = \passport\classes\RegCodeTemplate::validate($vcode);
			if (!$rst) {
				$data ['errorMsg']  = '手机验证码不正确.';
				$data ['errorType'] = 2;
			}
		}
		if (empty($data['errorMsg'])) {
			$user  = false;
			$cform = apply_filter('get_custom_join_form', null);
			if ($cform instanceof AbstractForm) {
				$cdata = $cform->valid();
				if (!$cdata) {
					$data['errorMsg']   = '表单数据错误';
					$data['formErrors'] = $cform->getErrors();
				} else {
					$user = $form->valid();
				}
			} else {
				$user = $form->valid();
			}
			if ($user) {
				$user ['group_id'] = intval(cfg('default_group@passport', 0));
				$user ['salt']     = rand_str(64);
				$user ['passwd']   = MemberModelForm::generatePwd($user['passwd'], $user['salt']);
				if (empty($user['username'])) {
					$user ['username'] = uniqid();
				}
				if (empty ($user ['nickname'])) {
					$user ['nickname'] = $type == 'mail' ? substr($user ['email'], 0, strpos($user ['email'], '@')) : 'g_' . $user ['phone'];
				}
				$user ['status']     = 1;
				$user ['registered'] = $user ['update_time'] = time();
				$user ['ip']         = Request::getIp();
				unset($user['captcha'], $user['vcode']);
				if (bcfg('mail_active@passport') && $type == 'mail') {
					$user['status'] = 2;
				}
				start_tran();
				$user_id = $this->registerMember($user, $model);
				if ($user_id) {
					commit_tran();
					$data['success'] = true;
					if (bcfg('mail_active@passport') && $type == 'mail') {
						$data['url'] = tourl('passport/active/' . $user_id);
					} else {
						$data['url'] = tourl('passport/done/' . $user_id);
					}
				} else {
					rollback_tran();
					$data['errorMsg'] = '系统内部错误';
				}
			} else {
				$data['formErrors'] = $form->getErrors();
			}
		}

		return new JsonView($data);
	}

	/**
	 * 注册会员.
	 *
	 * @param array           $user
	 * @param \db\model\Model $model
	 *
	 * @return int
	 */
	private function registerMember($user, $model) {
		$role_id = intval(cfg('default_role@passport', 0));
		$expire  = cfg('expire@passport', '0d');
		if (preg_match('/^(0|[1-9]\d*)(m|d)$/', $expire, $ms) && $ms[1] > 0) {
			if ($ms[2] == 'm') {
				$user['group_expire'] = strtotime(date('Y-m-d') . ' 23:59:59 +' . $ms[1] . ' months -1 day');
			} else {
				$user['group_expire'] = strtotime(date('Y-m-d') . ' 23:59:59 +' . $ms[1] . ' days');
			}
		}
		$user = apply_filter('before_member_created', $user);
		if (!$user) {
			return 0;
		}
		$model->removeValidateRule('captcha');
		if (isset($user['invite_code'])) {
			$invite_code = $user['invite_code'];
			unset($user['invite_code']);
			$model->removeValidateRule('invite_code');
			$mid                = $model->getField('mid', ['recommend_code' => $invite_code, 'status' => 1, 'deleted' => 0], 0);
			$user['invite_mid'] = $mid;
		} else {
			$user['invite_mid'] = 0;
		}
		$rst = $model->create($user);

		if ($rst) {
			$user ['mid'] = $rst;
			if ($role_id) {
				$model = new \passport\models\MemberMetaModel();
				$model->saveRoles($rst, [$role_id]);
			}
			$user = apply_filter('after_member_created', $user);
			if ($user) {
				return $rst;
			}
		} else {
			log_error(var_export($model->getErrors(), true), 'passport');
			log_error($model->lastSQL(), 'passport');
		}

		return 0;
	}
}