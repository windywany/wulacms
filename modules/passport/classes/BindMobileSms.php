<?php

namespace passport\classes;

use sms\classes\SMSTemplate;

class BindMobileSms extends SMSTemplate {
	private $code = null;
	public function getName() {
		return '绑定手机验证码';
	}
	public function getTemplate() {
		return '您正在绑定手机，验证码是{code}。';
	}
	public function getArgsDesc() {
		return [ 'code' => '手机验证码' ];
	}
	public function getArgs() {
		if (! $this->code) {
			$this->code = rand_str ( 6, '0-9' );
			$_SESSION ['bind_mobile_code'] = $this->code;
		}
		return [ 'code' => $this->code ];
	}
	public static function validate($code) {
		$code1 = sess_get ( 'bind_mobile_code' );
		return $code && $code1 && strtolower ( $code1 ) == strtolower ( $code );
	}
	public static function get_sms_templates($ts) {
		$ts ['bind_mobile'] = new BindMobileSms ();
		return $ts;
	}
}

?>