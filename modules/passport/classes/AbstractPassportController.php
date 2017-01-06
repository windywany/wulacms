<?php

namespace passport\classes;

abstract class AbstractPassportController extends \Controller {
	protected $checkUser = array('passport', 'vip');

	public function preRun($method) {
		parent::preRun($method);
		//加载会员数据
		$expire                   = $this->user['expire'];
		$this->user['upgradable'] = true;
		$this->user['buyGroup']   = false;
		if ($expire) {
			$time = time();
			if ($expire < $time) {
				$this->user['expireDate'] = '已于' . date('Y年m月d日', $expire) . '到期';
				$this->user['buyGroup']   = true;
			} else {
				if (($expire - $time) < 14 * 86400) {
					$this->user['buyGroup']   = true;
					$this->user['expireDate'] = '将于' . date('Y年m月d日', $expire) . '到期';
				} else {
					$this->user['expireDate'] = date('Y年m月d日', $expire) . '到期';
					$this->user['buyGroup']   = true;
				}
			}
		} else {
			$this->user['buyGroup']   = true;
			$this->user['expireDate'] = '已过期';
		}
		fire('load_member_data_for_passport', $this->user);
	}
}