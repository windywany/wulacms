<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/29 0029
 * Time: 下午 8:12
 */

namespace passport\models;

use db\model\FormModel;

class MemberModel extends FormModel {


	protected function createForm($data = []) {
		return new \MemberModelForm();
	}
}