<?php

class MemberModelForm extends AbstractForm {
	private $mid            = array('rules' => array('regexp(/^[0-9]+$/)' => '非法的用户编号.'));
	private $username       = array('rules' => array('required' => '请填写账户.', 'callback(@checkUsername,mid)' => '账户已经存在.'));
	private $email          = array('rules' => array('email' => '邮箱地址格式不合法.', 'callback(@checkEmail,mid)' => '邮箱已经存在.'));
	private $phone          = array('rules' => array('regexp(/^1[34578]\d{9}$/)' => '请填写正确的手机号', 'callback(@checkPhone,mid)' => '手机号已存在.'));
	private $passwd         = array('rules' => array('required' => '请填写密码.', 'minlength(6)' => '密码最少要%s位.'));
	private $passwd1        = array('rules' => array('equalTo(passwd)' => '二次输入的密码不一致.'));
	private $group_id       = array('rules' => array('digits' => '非法的用户组.'));
	private $nickname       = array('rules' => array('callback(@checkNickname)' => '昵称不可用'));
	private $status         = array('type' => 'int');
	private $invite_code    = array('rules' => array('callback(@checkInviteCode)' => '邀请人不存在.'));
	private $recommend_code = array('rules' => array('callback(@checkRecommendCode,mid)' => '推荐码已经存在.'));
	private $salt           = array();

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

	public function checkRecommendCode($value, $data, $message) {
		if (empty ($value)) {
			return true;
		}
		$user_id                  = $data ['mid'];
		$rst                      = dbselect()->from('{member}');
		$where ['recommend_code'] = $value;
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
	 * 保存用户角色.
	 *
	 * @param int   $mid
	 * @param array $roles
	 */
	public static function saveRoles($mid, $roles) {
		if ($mid) {
			dbdelete()->from('{member_has_role}')->where(array('mid' => $mid))->exec();
			$roleName = '';
			if (!empty ($roles)) {
				$datas = array();
				foreach ($roles as $role_id) {
					$datas [] = array('mid' => $mid, 'role_id' => $role_id, 'sort' => 0);
				}
				dbinsert($datas, true)->into('{member_has_role}')->exec();
				$roleNames = dbselect('role_name')->from('{user_role}')->where(array('role_id IN' => $roles))->toArray('role_name');
				$roleName  = implode(',', $roleNames);
			}
			dbsave(array('mid' => $mid, 'name' => 'roles', 'value' => $roleName), array('mid' => $mid, 'name' => 'roles'), 'mid')->into('{member_meta}')->exec();
		}
	}

	public static function generatePwd($password, $salt) {
		if ($salt) {
			return md5($salt . $password);
		} else {
			return md5($password);
		}
	}
}
