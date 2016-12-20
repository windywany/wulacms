<?php

class PointsHookImpl {
	/**
	 *
	 * @param AdminLayoutManager $layout
	 */
	public static function do_admin_layout($layout) {
		if (icando('r:points')) {
			$menu = new AdminNaviMenu ('points', '积分管理', 'fa-rub txt-color-purple');
			$menu->addSubmenu(array('points-acc', '积分帐户', 'fa-credit-card', tourl('points', false)), false, 1);
			$menu->addSubmenu(array('points-rec', '积分流水', 'fa-list', tourl('points/record', false)), false, 2);
			$menu->addSubmenu(array('points-type', '积分类型', 'fa-sitemap', tourl('points/type', false)), 'r:points/type', 3);

			$pmenu = $layout->getNaviMenu('finance');
			$pmenu->addItem($menu, 6);
		}
	}

	/**
	 * @param AclResourceManager $manager
	 *
	 * @return  mixed $manager
	 */
	public static function get_acl_resource($manager) {
		$acl = $manager->getResource('points', '积分管理');
		$acl->addOperate('r', '积分管理', '', true);
		$acl = $manager->getResource('points/type', '类型管理');
		$acl->addOperate('r', '列表', '', true);
		$acl->addOperate('a', '添加');
		$acl->addOperate('u', '修改');
		$acl->addOperate('d', '删除');

		return $manager;
	}

	public static function get_columns_of_pointsRecords($cols) {
		$cols['subject'] = ['name' => '项目', 'order' => 90, 'show' => true, 'width' => 100];
		$cols['expired'] = ['name' => '是否过期', 'order' => 91, 'show' => true, 'width' => 100, 'render' => function ($v, $data, $e) {
			if ($v) {
				return '是';
			}

			return '否';
		}];

		$cols['expire_time'] = ['name' => '过期时间', 'order' => 92, 'show' => false, 'width' => 100, 'render' => function ($v, $data, $e) {
			return date('Y-m-d', $v);
		}];

		$cols['note'] = ['name' => '备注', 'order' => 99, 'show' => false];

		return $cols;
	}
}