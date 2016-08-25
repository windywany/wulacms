<?php
/**
 * 用户表单.
 * @author Guangfeng
 *
 */
class SystemUserForm extends AbstractForm {
	private $email = array ('rules' => array ('required' => '请填写邮箱.','email' => '邮箱地址格式不合法.','callback(@checkEmail,user_id)' => '邮箱已经存在.' ) );
	private $group_id = array ('rules' => array ('digits' => '非法的用户组.' ) );
	private $nickname;
	private $passwd = array ('rules' => array ('required' => '请填写密码.','minlength(6)' => '密码最少要%s位.' ) );
	private $passwd1 = array ('rules' => array ('equalTo(passwd)' => '二次输入的密码不一致.' ) );
	private $roles;
	private $status;
	private $user_id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的用户编号.' ) );
	private $username = array ('rules' => array ('required' => '请填写账户.','callback(@checkUsername,user_id)' => '账户已经存在.' ) );
	/**
	 * 检测账户是否重复.
	 *
	 * @param string $value        	
	 * @param array $data        	
	 * @param string $message        	
	 * @return Ambigous <string, string, unknown>|boolean
	 */
	public function checkUsername($value, $data, $message) {
		$userDao = new UserDao ();
		$rst = $userDao->checkDuplicate ( 'username', $value, $data ['user_id'] );
		if (! $rst) {
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
		$userDao = new UserDao ();
		$rst = $userDao->checkDuplicate ( 'email', $value, $data ['user_id'] );
		if (! $rst) {
			return $message;
		}
		return true;
	}
}