<?php

namespace passport\forms;

class PhoneSignUpForm extends MailSignUpForm {
	private $phone    = array('rules' => array('required' => '请填写手机号', 'regexp(/^1[34578]\d{9}$/)' => '请填写正确的手机号', 'callback(@checkPhone)' => '手机号已存在.'));
	private $username = array('rules' => array('callback(@checkUsername)' => '账户已经存在.'));
	private $nickname = array('rules' => array('callback(@checkNickname)' => '昵称不可用'));
	private $passwd   = array('rules' => array('required' => '请填写密码.', 'minlength(6)' => '密码最少要%s位.'));
}