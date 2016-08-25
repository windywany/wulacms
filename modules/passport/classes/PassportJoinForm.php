<?php
class PassportJoinForm extends AbstractForm {
	private $user_id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的用户编号.' ) );
	private $email = array ('rules' => array ('required' => '请填写邮箱.','email' => '邮箱地址格式不合法.','callback(@checkEmail)' => '邮箱已经存在.' ) );
	private $passwd = array ('rules' => array ('required' => '请填写密码.','minlength(6)' => '密码最少要%s位.' ) );
	private $passwd1 = array ('rules' => array ('equalTo(passwd)' => '二次输入的密码不一致.' ) );
	private $username = array ('rules' => array ('callback(@checkUsername)' => '账户已经存在.' ) );
	private $phone = array ('rules' => array ('required' => '请填写手机号.','regexp(/^1[345678]\d{9}$/)' => '请填写正确的手机号','callback(@checkPhone)' => '手机号已存在.' ) );
	private $phone_code = array ('skip' => true,'rules' => array ('required' => '请填写动态验证码','callback(@checkPhoneCode)' ) );
	private $captcha = array ('rules' => array ('required' => '请填写验证码.','callback(@checkCaptcha)' => '验证码错误.' ) );
	private $invite_code = array ('rules' => array ('callback(@checkInviteCode)' => '邀请码不存在.' ) );
	private $nickname = array ();
	/**
	 * 检测账户是否重复.
	 *
	 * @param string $value        	
	 * @param array $data        	
	 * @param string $message        	
	 * @return Ambigous <string, string, unknown>|boolean
	 */
	public function checkUsername($value, $data, $message) {
		if (empty ( $value )) {
			return true;
		}
		$user_id = $data ['user_id'];
		$rst = dbselect ()->from ( '{member}' );
		$where ['username'] = $value;
		$rst->where ( $where );
		if ($rst->exist ( 'mid' )) {
			return $message;
		}
		return true;
	}
	/**
	 * 检测邮箱是否重复.
	 *
	 * @param string $value        	
	 * @param array $data        	
	 * @param string $message        	
	 * @return Ambigous <string, string, unknown>|boolean
	 */
	public function checkEmail($value, $data, $message) {
		$rst = dbselect ()->from ( '{member}' );
		$where ['email'] = $value;
		$rst->where ( $where );
		if ($rst->exist ( 'mid' )) {
			return $message;
		}
		return true;
	}
	public function checkCaptcha($value, $data, $message) {
		if (bcfg ( 'enable_captcha@passport', true )) {
			$auth_code_obj = new CaptchaCode ();
			if (! $auth_code_obj->validate ( $value, false )) {
				return $message;
			}
		}
		return true;
	}
	/**
	 * 检测手机号是否重复.
	 *
	 * @param string $value        	
	 * @param array $data        	
	 * @param string $message        	
	 * @return Ambigous <string, string, unknown>|boolean
	 */
	public function checkPhone($value, $data, $message) {
		if (empty ( $value )) {
			return true;
		}
		$rst = dbselect ()->from ( '{member}' );
		$where ['phone'] = $value;
		$rst->where ( $where );
		if ($rst->exist ( 'mid' )) {
			return $message;
		}
		return true;
	}
	public function checkPhoneCode($value, $data, $message) {
		// TODO: 完成手机动态验证码功能.
		return true;
	}
	public function checkInviteCode($value, $data, $message) {
		$value = trim ( $value );
		if (bcfg ( 'enable_invation@passport' ) && bcfg ( 'invite_required@passport' )) {
			if (empty ( $value )) {
				return '请填写邀请码.';
			}
		}
		if(empty($value)){
			return true;
		}
		if (! dbselect ()->from ( '{member}' )->where ( array ('recommend_code' => $value ) )->exist ( 'mid' )) {
			return $message;
		}
		return true;
	}
}
