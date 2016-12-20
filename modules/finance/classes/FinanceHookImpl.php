<?php

class FinanceHookImpl {
	/**
	 *
	 * @param AdminLayoutManager $layout
	 */
	public static function do_admin_layout($layout) {
		if (icando('r:finance')) {
			$menu    = new AdminNaviMenu ('finance', '财务', 'fa-money txt-color-green');
			$member  = new AdminNaviMenu ('financemember', '账户列表', 'fa-credit-card', tourl('finance', false));
			$deposit = new AdminNaviMenu ('findeposit', '充值记录', 'fa-sign-in', tourl('finance/deposit', false));

			$withdraw = new AdminNaviMenu ('finwithdraw', '提现管理', 'fa-sign-out');
			$withdraw->addSubmenu(['withdraw-pending', '待审核', 'fa-sign-out', tourl('finance/withdraw', false)], false, 1);
			$withdraw->addSubmenu(['withdraw-approve', '待付款', 'fa-sign-out txt-color-blue', tourl('finance/withdraw', false)], false, 2);
			$withdraw->addSubmenu(['withdraw-pay', '已付款', 'fa-sign-out txt-color-green', tourl('finance/withdraw', false)], false, 3);
			$withdraw->addSubmenu(['withdraw-refuse', '已拒绝', 'fa-sign-out txt-color-red', tourl('finance/withdraw', false)], false, 4);

			$menu->addItem($member, 'r:finance/finance', 1);
			$menu->addItem($deposit, 'r:finance/deposit', 2);
			$menu->addItem($withdraw, 'r:finance/withdraw', 3);
			$layout->addNaviMenu($menu, 22);
		}

		if (icando('m:system/preference')) {
			$sysMenu     = $layout->getNaviMenu('system');
			$settingMenu = $sysMenu->getItem('preferences');
			$settingMenu->addSubmenu(array('financepreference', '支付通道设置', 'fa-cog', tourl('finance/preference', false)), 'finance:system/preference');
		}
	}

	/**
	 *
	 * @param AclResourceManager $manager
	 *
	 * @return  mixed $manager
	 */
	public static function get_acl_resource($manager) {
		$acl = $manager->getResource('system/preference');
		$acl->addOperate('finance', '支付设置');

		$acl = $manager->getResource('finance', '财务管理');
		$acl->addOperate('r', '财务管理', '', true);

		$acl = $manager->getResource('finance/finance', '用户列表');
		$acl->addOperate('r', '财务列表', '', true);
		$acl->addOperate('u', '财务修改');
		$acl = $manager->getResource('finance/deposit', '充值列表');
		$acl->addOperate('r', '充值列表', '', true);
		$acl->addOperate('u', '充值修改');
		$acl = $manager->getResource('finance/withdraw', '提现列表');
		$acl->addOperate('r', '提现列表', '', true);
		$acl->addOperate('u', '提现修改');

		return $manager;
	}
}