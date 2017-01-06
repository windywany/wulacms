<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace pay\classes;

class PayHookImpl {
	/**
	 *
	 * @param \AdminLayoutManager $layout
	 */
	public static function do_admin_layout($layout) {
		if (icando('pay:system/preference')) {
			$sysMenu     = $layout->getNaviMenu('system');
			$settingMenu = $sysMenu->getItem('preferences');
			$settingMenu->addSubmenu(array('financepreference', '支付设置', 'fa-money', tourl('pay/preference', false)), 'pay:system/preference');
		}
	}

	/**
	 *
	 * @param \AclResourceManager $manager
	 *
	 * @return  mixed $manager
	 */
	public static function get_acl_resource($manager) {
		$acl = $manager->getResource('system/preference');
		$acl->addOperate('pay', '支付设置');

		return $manager;
	}
}