<?php

namespace passport\models;

use db\model\FormModel;
use passport\models\param\RegisterParam;

class MemberModel extends FormModel {
	/**
	 * 注册会员.
	 *
	 * @param \passport\models\param\RegisterParam $param
	 *
	 * @return int 会员ID,失败返回0.
	 */
	public function register(RegisterParam $param) {
		return 0;
	}

	protected function createForm($data = []) {
		return new \MemberModelForm();
	}
}