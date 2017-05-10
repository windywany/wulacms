<?php

namespace passport\classes;

use sms\classes\SMSTemplate;

class ResetPasswdSms extends SMSTemplate {
	private $code = null;

	public function getName() {
		return '找回密码手机验证码';
	}

	public function getTemplate() {
		return '您正在找回密码，验证码是{code}。';
	}

	public function getArgsDesc() {
		return ['code' => '手机验证码'];
	}

	public function getArgs() {
		if (!$this->code) {
			if ($this->testMode) {
				$this->code = '123456';
			} else {
				$this->code = rand_str(6, '0-9');
			}
		}

		return ['code' => $this->code];
	}

	public function onSuccess() {
		$_SESSION ['reset_passwd_code']   = $this->code;
		$_SESSION ['reset_passwd_expire'] = time() + $this->getTimeout();
	}

	public static function validate($code) {
		$code1 = sess_get('reset_passwd_code');
		$time1 = sess_get('reset_passwd_expire', 0);
		if ($time1 > time()) {
			if ($code && strtolower($code1) == strtolower($code)) {
				sess_del('reset_passwd_expire');
				sess_del('reset_passwd_code');

				return true;
			}
		} else {
			sess_del('reset_passwd_expire');
			sess_del('reset_passwd_code');
		}

		return false;
	}

	public static function get_sms_templates($ts) {
		$ts ['reset_passwd'] = new ResetPasswdSms ();

		return $ts;
	}
}