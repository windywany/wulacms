<?php

class CoinsHookImpl {
	/**
	 *
	 * @param AdminLayoutManager $layout
	 */
	public static function do_admin_layout($layout) {
		if (icando('r:coins')) {
			$menu = new AdminNaviMenu ('coins', '金币管理', 'fa-btc txt-color-orange');
			$menu->addSubmenu(array('coins-acc', '金币帐户', 'fa-credit-card', tourl('coins', false)), false, 1);
			$menu->addSubmenu(array('coins-rec', '金币流水', 'fa-list', tourl('coins/record', false)), false, 2);
			$menu->addSubmenu(array('coins-type', '金币类型', 'fa-sitemap', tourl('coins/type', false)), 'r:coins/type', 3);
			$menu->addSubmenu(array('coins-setting', '金币设置', 'fa-gear', tourl('coins/preference', false)), 'r:coins/type', 4);

			$pmenu = $layout->getNaviMenu('finance');
			$pmenu->addItem($menu, 4);
		}
	}

	/**
	 * @param AclResourceManager $manager
	 *
	 * @return  mixed $manager
	 */
	public static function get_acl_resource($manager) {
		$acl = $manager->getResource('coins', '金币管理');
		$acl->addOperate('r', '金币管理', '', true);
		$acl = $manager->getResource('coins/type', '类型管理');
		$acl->addOperate('r', '列表', '', true);
		$acl->addOperate('a', '添加');
		$acl->addOperate('u', '修改');
		$acl->addOperate('d', '删除');
		$acl = $manager->getResource('coins/preference', '类型管理');
		$acl->addOperate('r', '列表', '', true);
		$acl->addOperate('u', '修改');

		return $manager;
	}
}