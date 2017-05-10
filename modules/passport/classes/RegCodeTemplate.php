<?php
namespace passport\classes;

use sms\classes\SMSTemplate;

class RegCodeTemplate extends SMSTemplate {

	private $code = null;

	public static function get_sms_templates($tpls) {
		$tpls ['regcode'] = new RegCodeTemplate ();

		return $tpls;
	}

	public function getTemplate() {
		return '验证码是：{code},请不要把验证码透漏给其他人。';
	}

	public function getArgsDesc() {
		return ['code' => '验证码'];
	}

	/*
	 * (non-PHPdoc) @see \sms\classes\SMSTemplate::getArgs()
	 */
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
		$_SESSION ['reg_verify_code']   = $this->code;
		$_SESSION ['reg_verify_expire'] = time() + $this->getTimeout();
	}

	public function getName() {
		return '注册验证码';
	}

	public static function validate($code) {
		$code1 = sess_get('reg_verify_code');
		$time1 = sess_get('reg_verify_expire', 0);
		if ($time1 > time()) {
			if ($code && strtolower($code1) == strtolower($code)) {
				sess_del('reg_verify_expire');
				sess_del('reg_verify_code');

				return true;
			}
		} else {
			sess_del('reg_verify_expire');
			sess_del('reg_verify_code');

		}

		return false;
	}
}