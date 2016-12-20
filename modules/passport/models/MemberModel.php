<?php

namespace passport\models;

use db\model\FormModel;
use passport\models\param\ChpasswdParam;
use passport\models\param\RegisterParam;

class MemberModel extends FormModel {
	protected function config() {
		$this->primaryKeys = ['mid'];
	}

	/**
	 * @param integer $mid
	 * @param bool    $safe
	 *
	 * @return array
	 */
	public function loadMemberInfo($mid, $safe = true) {
		$user = $this->get($mid);
		if ($user) {
			$mm    = new MemberMetaModel();
			$metas = $mm->getArray(array('mid' => $user ['mid']), 'value', 'name');
			unset($user['passwd'], $user['salt'], $user['deleted'], $user['username']);
			if ($metas) {
				$user = array_merge($metas, $user);
			}
		}
		if ($safe) {
			unset($user['passwd'], $user['salt'], $user['deleted'], $user['username']);
		}

		return $user;
	}

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

	/**
	 * 修改用户密码.
	 *
	 * @param \passport\models\param\ChpasswdParam $param
	 *
	 * @return bool 修改成功返回true,失败返回false.
	 */
	public function chpasswd(ChpasswdParam $param) {
		$data = $param->toArray();

		return false;
	}

	protected function createForm($data = []) {
		return new \MemberModelForm();
	}
}