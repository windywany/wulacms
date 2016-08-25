<?php
/**
 * 用户表单.
 * @author Guangfeng
 *
 */
class UserProfileForm extends AbstractForm {
	private $email = array ('rules' => array ('required' => '请填写邮箱.','email' => '邮箱地址格式不合法.','callback(@checkEmail,user_id)' => '邮箱已经存在.' ) );
	private $nickname = array ('rules' => array ('required' => '请填写你的姓名.' ) );
	private $passwd = array ('rules' => array ('minlength(6)' => '密码最少要%s位.' ) );
	private $passwd1 = array ('rules' => array ('equalTo(passwd)' => '二次输入的密码不一致.' ) );
	private $user_id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的用户编号.' ) );
	/**
	 * 检测邮箱是否重复.
	 *
	 * @param string $value        	
	 * @param array $data        	
	 * @param string $message        	
	 * @return Ambigous <string, string, unknown>|boolean
	 */
	public function checkEmail($value, $data, $message) {
		$rst = dbselect ( 'user_id' )->from ( '{user}' );
		$where ['email'] = $value;
		if (! empty ( $data ['user_id'] )) {
			$where ['user_id !='] = $data ['user_id'];
		}
		$rst->where ( $where );
		if ($rst->count ( 'user_id' ) > 0) {
			return $message;
		}
		return true;
	}
	public static function saveUserMeta($user_id, $name, $value) {
		$meta ['meta_value'] = $value;
		$meta_id = dbselect ( 'meta_id,meta_value' )->from ( '{user_meta}' )->where ( array ('user_id' => $user_id,'meta_name' => $name ) )->get ();
		if ($meta_id) {
			if ($value != $meta_id ['meta_value']) {
				dbupdate ( '{user_meta}' )->set ( $meta )->where ( array ('meta_id' => $meta_id ['meta_id'] ) )->exec ();
			}
		} else {
			$meta ['user_id'] = $user_id;
			$meta ['meta_name'] = $name;
			dbinsert ( $meta )->into ( '{user_meta}' )->exec ();
		}
	}
}