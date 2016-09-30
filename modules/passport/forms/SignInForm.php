<?php

namespace passport\forms;

class SignInForm extends \AbstractForm {
	private $username = array('rules' => array('required' => '请输入账户.'));
	private $passwd   = array('rules' => array('required' => '请输入密码.', 'minlength(6)' => '密码的最小长度为6个字符.'));
}