<?php

namespace passport\models;

use db\model\Model;
use passport\models\param\SignInParam;

class MemberOauthModel extends Model {
	/**
	 * 第三方登录.
	 *
	 * @param \passport\models\param\SignInParam $param
	 *
	 * @return array
	 */
	public function signIn(SignInParam $param) {
		return array();
	}
}