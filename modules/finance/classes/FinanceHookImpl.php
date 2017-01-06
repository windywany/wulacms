<?php

class FinanceHookImpl {
	/**
	 *
	 * @param AdminLayoutManager $layout
	 */
	public static function do_admin_layout($layout) {
		if (icando('r:finance')) {
			$menu    = new AdminNaviMenu ('finance', '财务', 'fa-money txt-color-red');
			$member  = new AdminNaviMenu ('financemember', '账户列表', 'fa-credit-card', tourl('finance', false));
			$deposit = new AdminNaviMenu ('findeposit', '充值记录', 'fa-sign-in', tourl('finance/deposit', false));
			$menu->addItem($member, 'r:finance/finance', 1);
			$menu->addItem($deposit, 'r:finance/deposit', 2);

			if (bcfg('allow_withdraw@finance')) {
				$withdraw = new AdminNaviMenu ('finwithdraw', '提现管理', 'fa-sign-out');
				$withdraw->addSubmenu(['withdraw-pending', '待审核', 'fa-sign-out', tourl('finance/withdraw/0', false)], false, 1);
				$withdraw->addSubmenu(['withdraw-approve', '待付款', 'fa-sign-out txt-color-blue', tourl('finance/withdraw/1', false)], false, 2);
				$withdraw->addSubmenu(['withdraw-pay', '已付款', 'fa-sign-out txt-color-green', tourl('finance/withdraw/3', false)], false, 3);
				$withdraw->addSubmenu(['withdraw-refuse', '已拒绝', 'fa-sign-out txt-color-red', tourl('finance/withdraw/2', false)], false, 4);
				$menu->addItem($withdraw, 'r:finance/withdraw', 3);
			}

			$layout->addNaviMenu($menu, 22);
		}
	}

	/**
	 *
	 * @param AclResourceManager $manager
	 *
	 * @return  mixed $manager
	 */
	public static function get_acl_resource($manager) {
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

	public static function get_columns_of_depositTable($cols) {

		$cols['orderid']  = ['name' => '订单ID', 'width' => 100, 'show' => true, 'order' => 70];
		$cols['subject']  = ['name' => '项目', 'width' => 100, 'show' => true, 'order' => 80];
		$cols['device']   = ['name' => '设备', 'width' => 80, 'show' => true, 'order' => 81];
		$cols['channel']  = ['name' => '渠道', 'width' => 80, 'show' => true, 'order' => 82];
		$cols['platform'] = ['name' => '充值平台', 'width' => 80, 'show' => true, 'order' => 89];
		$cols['transid']  = ['name' => '交易ID', 'width' => 120, 'show' => false, 'order' => 90];

		$cols['confirmed']       = ['name' => '入账时间', 'width' => 130, 'show' => false, 'order' => 91, 'render' => function ($v, $data, $e) {
			return date('Y-m-d H:i', $v);
		}];
		$cols['order_confirmed'] = ['name' => '处理时间', 'width' => 130, 'show' => false, 'order' => 92, 'render' => function ($v, $data, $e) {
			return date('Y-m-d H:i', $v);
		}];
		$cols['checked']         = ['name' => '对账时间', 'width' => 130, 'show' => false, 'order' => 93, 'render' => function ($v, $data, $e) {
			return date('Y-m-d H:i', $v);
		}];

		return $cols;
	}

	public static function get_columns_of_withdrawTable($cols) {
		$cols['account']         = ['name' => '帐户', 'width' => 150, 'show' => true, 'order' => 70];
		$cols['tax_amount']      = ['name' => '税率/税费', 'width' => 120, 'order' => 85, 'show' => false];
		$cols['discount']        = ['name' => '费率/手续费', 'width' => 120, 'order' => 80, 'show' => true];
		$cols['approve_message'] = ['name' => '审核备注', 'order' => 99, 'show' => false];

		return $cols;
	}

	public static function get_artisan_commands($cmds) {
		$cmds['fnotify'] = new \finance\command\NotifyCommand();

		return $cmds;
	}

	public static function load_member_data_for_passport(Passport $user) {
		$mid     = $user->getUid();
		$model   = new \finance\models\MemberFinanceAccountModel();
		$account = $model->getAccount($mid);
		if ($account) {
			$user['finance'] = $account;
		} else {
			$user['finance'] = [];
		}
	}
}