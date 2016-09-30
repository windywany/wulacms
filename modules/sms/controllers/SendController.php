<?php

namespace sms\controllers;

use sms\classes\Sms;

class SendController extends \Controller {

	public function index($phone, $tpl, $captcha = '') {
		$data['success']   = false;
		$data ['errorMsg'] = '';
		if (bcfg('captcha_enabled@sms', true)) {
			$auth_code_obj = new \CaptchaCode ();
			if (!$auth_code_obj->validate($captcha, false, false)) {
				$data ['errorMsg']  = '验证码不正确.';
				$data ['errorType'] = 1;
			}
		}

		if (empty($data['errorMsg'])) {
			$args            = [];
			$rst             = Sms::send($phone, $tpl, $args);
			$data['success'] = $rst;
			if ($rst) {
				$data['timeout'] = $args['exp'];
			}
		}

		return new \JsonView($data);
	}
}