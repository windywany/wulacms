<?php
class MemberModelForm extends AbstractForm {
	private $mid = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的用户编号.' ) );
	private $username = array ('rules' => array ('required' => '请填写账户.','callback(@checkUsername,mid)' => '账户已经存在.' ) );
	private $email = array ('rules' => array ('email' => '邮箱地址格式不合法.','callback(@checkEmail,mid)' => '邮箱已经存在.' ) );
	private $phone = array ('rules' => array ('regexp(/^1[345678]\d{9}$/)' => '请填写正确的手机号','callback(@checkPhone,mid)' => '手机号已存在.' ) );
	private $passwd = array ('rules' => array ('required' => '请填写密码.','minlength(6)' => '密码最少要%s位.' ) );
	private $passwd1 = array ('rules' => array ('equalTo(passwd)' => '二次输入的密码不一致.' ) );
	private $group_id = array ('rules' => array ('digits' => '非法的用户组.' ) );
	private $role_id = array ('rules' => array ('digits' => '非法的用户组.' ) );
	private $type = array ('rules' => array ('required' => '请选择类型' ) );
	private $nickname;
	private $status = array ('type' => 'int' );
	private $invite_code = array ('rules' => array ('callback(@checkInviteCode)' => '邀请人不存在.' ) );
	private $recommend_code = array ('rules' => array ('callback(@checkRecommendCode,mid)' => '推荐码已经存在.' ) );
	/**
	 * 检测账户是否重复.
	 *
	 * @param string $value
	 * @param array $data
	 * @param string $message
	 * @return Ambigous <string, string, unknown>|boolean
	 */
	public function checkUsername($value, $data, $message) {
		$user_id = $data ['mid'];
		$rst = dbselect ()->from ( '{member}' );
		$where ['username'] = $value;
		if (! empty ( $user_id )) {
			$where ['mid !='] = $user_id;
		}
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
		if (empty ( $value )) {
			return true;
		}
		$user_id = $data ['mid'];
		$rst = dbselect ()->from ( '{member}' );
		$where ['email'] = $value;
		if (! empty ( $user_id )) {
			$where ['mid !='] = $user_id;
		}
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
	public function checkPhone($value, $data, $message) {
		if (empty ( $value )) {
			return true;
		}
		$user_id = $data ['mid'];
		$rst = dbselect ()->from ( '{member}' );
		$where ['phone'] = $value;
		if (! empty ( $user_id )) {
			$where ['mid !='] = $user_id;
		}
		$rst->where ( $where );
		if ($rst->exist ( 'mid' )) {
			return $message;
		}
		return true;
	}
	public function checkInviteCode($value, $data, $message) {
		if (empty ( $value )) {
			return true;
		}
		if (! dbselect ()->from ( '{member}' )->where ( array ('recommend_code' => $value ) )->exist ( 'mid' )) {
			return $message;
		}
		return true;
	}
	public function checkRecommendCode($value, $data, $message) {
		if (empty ( $value )) {
			return true;
		}
		$user_id = $data ['mid'];
		$rst = dbselect ()->from ( '{member}' );
		$where ['recommend_code'] = $value;
		if (! empty ( $user_id )) {
			$where ['mid !='] = $user_id;
		}
		$rst->where ( $where );
		if ($rst->exist ( 'mid' )) {
			return $message;
		}
		return true;
	}
	/**
	 * 取通行中会员类型.通过插件机制扩展.
	 *
	 * @uses filter - get_passport_member_types
	 * @return array
	 */
	public static function getMemberTypes() {
		$types = array ('' => '-类型-','P' => '个人' );
		$types = apply_filter ( 'get_passport_member_types', $types );
		return $types;
	}
	public static function getCustomeFields(&$form) {
		$widgets = array ();
		$fields = apply_filter ( 'load_member_fields', array () );
		foreach ( $fields as $field ) {
			if (isset ( $field ['rules'] ) && $field ['rules']) {
				$form [$field ['name']] = array ('rules' => $field ['rules'],'skip' => true );
			} else if (isset ( $field ['required'] ) && $field ['required']) {
				$form [$field ['name']] = array ('rules' => array ('required' => $field ['label'] . '必须填写.' ),'skip' => true );
			}
			if (isset ( $field ['group'] ) && empty ( $field ['group'] )) {
				unset ( $field ['group'], $field ['col'] );
			}
			$widgets [] = $field;
		}
		return $widgets;
	}
	/**
	 * 保存用户角色.
	 *
	 * @param int $mid
	 * @param array $roles
	 */
	public static function saveRoles($mid, $roles) {
		if ($mid) {
			dbdelete ()->from ( '{member_has_role}' )->where ( array ('mid' => $mid ) )->exec ();
			$roleName = '';
			if (! empty ( $roles )) {
				$datas = array ();
				foreach ( $roles as $role_id ) {
					$datas [] = array ('mid' => $mid,'role_id' => $role_id,'sort' => 0 );
				}
				dbinsert ( $datas, true )->into ( '{member_has_role}' )->exec ();
				$roleNames = dbselect ( 'role_name' )->from ( '{user_role}' )->where ( array ('role_id IN' => $roles ) )->toArray ( 'role_name' );
				$roleName = implode ( ',', $roleNames );
			}
			dbsave ( array ('mid' => $mid,'name' => 'roles','value' => $roleName ), array ('mid' => $mid,'name' => 'roles' ), 'mid' )->into ( '{member_meta}' )->exec ();
		}
	}
}
