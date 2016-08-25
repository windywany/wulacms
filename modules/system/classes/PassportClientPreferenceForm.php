<?php
/**
 * 通行证客户端设置。
 * @author ngf
 *
 */
class PassportClientPreferenceForm {
	/**
	 *
	 * @param DynamicForm $form        	
	 */
	public static function init($form) {
		$form ['enable_client'] = array ('label' => '接入通行证系统','widget' => 'radio','default' => '0','defaults' => "0=不接入\n1=接入",'note' => '通行证系统类型必须为管理员。' );
		$form ['login_url'] = array ('label' => '通行证服务器登录URL','default' => '','rules' => array ('required(enable_client_1:checked:1)' => '通行证服务器登录URL','url' => '请填写正确的URL。' ) );
		$form ['url'] = array ('label' => '通行证服务器接口URL','default' => '','rules' => array ('required(enable_client_1:checked:1)' => '请填写通行证服务器接口URL','url' => '请填写正确的URL。' ) );
		$form ['appkey'] = array ('group' => '1','col' => '6','label' => '应用ID','rules' => array ('required(enable_client_1:checked:1)' => '请填写应用ID' ) );
		$form ['appsecret'] = array ('group' => '1','col' => '6','label' => '应用安全码','rules' => array ('required(enable_client_1:checked:1)' => '请填写应用安全码' ) );
		$form ['default_group'] = array ('group' => '2','col' => '6','label' => '默认用户组','widget' => 'auto','defaults' => 'user_group,group_id,group_name,pst:system/preference','note' => '用户注册时将用户添加到该组。' );
		$form ['default_role'] = array ('group' => '2','col' => '6','label' => '默认角色','widget' => 'auto','defaults' => 'user_role,role_id,role_name,pst:system/preference','note' => '用户注册后用户拥有该角色。' );
		$pform = new PassportClientPreferenceForm ();
		$form->registerCallback ( 'formatDefault_roleValue', array ($pform,'formatDefault_roleValue' ) );
		$form->registerCallback ( 'formatDefault_groupValue', array ($pform,'formatDefault_groupValue' ) );
	}
	public function formatDefault_roleValue($value) {
		if ($value) {
			$name = dbselect ()->from ( '{user_role}' )->where ( array ('role_id' => $value ) )->get ( 'role_name' );
			if ($name) {
				return $value . ':' . $name;
			}
		}
		return $value;
	}
	public function formatDefault_groupValue($value) {
		if ($value) {
			$name = dbselect ()->from ( '{user_group}' )->where ( array ('group_id' => $value ) )->get ( 'group_name' );
			if ($name) {
				return $value . ':' . $name;
			}
		}
		return $value;
	}
}