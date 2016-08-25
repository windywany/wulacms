<?php

/**
 * Login Form.
 * @author Leo Ning
 *
 */
class AuthForm extends AbstractForm {
	private $username = array (
			'rules' => array (
					'required'=>'请输入用户名或邮箱.' 
			) 
	);
	private $passwd = array (
			'rules' => array (
					'required'=>'请输入密码.',
					'minlength(6)'=>'密码的最小长度为6个字符.' 
			) 
	);
}
?>