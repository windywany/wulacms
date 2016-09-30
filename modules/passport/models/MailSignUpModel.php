<?php

namespace passport\models;

use db\model\FormModel;
use passport\forms\MailSignUpForm;

class MailSignUpModel extends FormModel {
	public $table = 'member';

	protected function config() {
		$this->rules['status'] = array('regexp(/^(0|1|2|3)$/)' => '状态码只能是0,1,2,3');
		$this->rules['ip']     = array('required' => 'IP不能为空');
	}

	protected function createForm($data = []) {
		return new MailSignUpForm($data);
	}
}