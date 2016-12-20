<?php

namespace passport\forms;

class MailSignUpForm extends \AbstractForm {
	private $email    = array('rules' => array('required' => '请填写邮箱.', 'email' => '邮箱地址格式不合法.', 'callback(@checkEmail)' => '邮箱已经存在.'));
	private $username = array('rules' => array('callback(@checkUsername)' => '账户已经存在.'));
	private $nickname = array('rules' => array('callback(@checkNickname)' => '昵称不可用'));
	private $passwd   = array('rules' => array('required' => '请填写密码.', 'minlength(6)' => '密码最少要%s位.'));

	public function init_form_fields($data, $value_set) {
		if (bcfg('enable_invation@passport')) {
			$ic = ['rules' => []];
			if (bcfg('invite_required@passport')) {
				$ic['rules']['required'] = '请填写邀请码';
			}
			$ic['rules']['callback(@checkInviteCode)'] = '邀请码不可用.';
			$this->addField('invite_code', $ic);
		}
		if (bcfg('enable_captcha@passport', true)) {
			$ic                      = ['rules' => []];
			$ic['rules']['required'] = '请填写验证码';
			$this->addField('captcha', $ic);
		}
	}

	public function checkNickname($v, $d, $m) {
		if (empty($v)) {
			return true;
		}
		if (dbselect('id')->from('{member_nickname_black}')->where(['nickname' => $v])->exist('id')) {
			return $m;
		}

		return true;
	}

	/**
	 * 检测账户是否重复.
	 *
	 * @param string $value
	 * @param array  $data
	 * @param string $message
	 *
	 * @return mixed
	 */
	public function checkUsername($value, $data, $message) {
		$user_id            = $data ['mid'];
		$rst                = dbselect('mid')->from('{member}');
		$where ['username'] = $value;
		if (!empty ($user_id)) {
			$where ['mid !='] = $user_id;
		}
		$rst->where($where);
		if ($rst->count('mid')) {
			return $message;
		}

		return true;
	}

	/**
	 * 检测邮箱是否重复.
	 *
	 * @param string $value
	 * @param array  $data
	 * @param string $message
	 *
	 * @return mixed
	 */
	public function checkEmail($value, $data, $message) {
		if (empty ($value)) {
			return true;
		}
		$user_id         = $data ['mid'];
		$rst             = dbselect()->from('{member}');
		$where ['email'] = $value;
		if (!empty ($user_id)) {
			$where ['mid !='] = $user_id;
		}
		$rst->where($where);
		if ($rst->exist('mid')) {
			return $message;
		}

		return true;
	}

	/**
	 * 检测邮箱是否重复.
	 *
	 * @param string $value
	 * @param array  $data
	 * @param string $message
	 *
	 * @return mixed
	 */
	public function checkPhone($value, $data, $message) {
		if (empty ($value)) {
			return true;
		}
		$user_id         = $data ['mid'];
		$rst             = dbselect()->from('{member}');
		$where ['phone'] = $value;
		if (!empty ($user_id)) {
			$where ['mid !='] = $user_id;
		}
		$rst->where($where);
		if ($rst->exist('mid')) {
			return $message;
		}

		return true;
	}

	public function checkInviteCode($value, $data, $message) {
		if (empty ($value)) {
			return true;
		}
		if (!dbselect()->from('{member}')->where(array('recommend_code' => $value))->exist('mid')) {
			return $message;
		}

		return true;
	}
}