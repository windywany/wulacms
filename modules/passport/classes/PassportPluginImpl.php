<?php

/**
 * 通行证实现的hooks。
 * @author ngf
 *
 */
class PassportPluginImpl {
	/**
	 * 注册导航菜单.
	 *
	 * @param AdminLayoutManager $layout
	 */
	public static function do_admin_layout($layout) {
		if (icando('m:account') && icando('m:account/member')) {
			$acc  = $layout->getNaviMenu('account');
			$menu = new AdminNaviMenu ('memlist', '会员管理', 'fa-user', tourl('passport/members', false));

			$menub = new AdminNaviMenu ('vip-group', '会员分组', 'fa-user', tourl('system/group/vip', false));
			$menu->addItem($menub, false, 1);

			$menub = new AdminNaviMenu ('vip-role', '会员角色', 'fa-user', tourl('system/role/vip', false));
			$menu->addItem($menub, false, 2);

			$menub = new AdminNaviMenu ('memblacklist', '昵称黑名单', 'fa-user txt-color-red', tourl('passport/black', false));
			$menu->addItem($menub, false, 3);

			$menub = new AdminNaviMenu ('vip-login', '通行证', 'fa-key', tourl('passport/users', false));
			$menu->addItem($menub, false, 4);

			$acc->addItem($menu, false);
		}
		if (icando('m:system/preference')) {
			$sysMenu     = $layout->getNaviMenu('system');
			$settingMenu = $sysMenu->getItem('preferences');
			$settingMenu->addSubmenu(array('syspassport', '通行证设置', 'fa-cog', tourl('passport/preference', false)), 'pst:system/preference');
		}
	}

	public static function get_rbac_driver($driver, $type) {
		if ($driver == null && $type == 'vip') {
			$driver = new PassportRbacDriver ();
		}

		return $driver;
	}

	/**
	 * 注册passport服务.
	 *
	 * @param RestServer $server
	 *
	 * @return RestServer
	 */
	public static function on_init_rest_server($server) {
		return $server;
	}

	/**
	 * @param AclResourceManager $manager
	 *
	 * @return mixed
	 */
	public static function get_acl_resource($manager) {
		$acl = $manager->getResource('account/member', '会员管理');
		$acl->addOperate('r', '会员管理', '', true);
		$acl->addOperate('u', '编辑会员');
		$acl->addOperate('d', '删除会员');
		$acl->addOperate('a', '审核会员');
		// 系统配置
		$acl = $manager->getResource('system/preference', '系统配置');
		$acl->addOperate('pst', '通行证设置');

		return $manager;
	}

	public static function get_default_apps($apps) {
		$apps ['passport'] = '通行证';

		return $apps;
	}

	public static function get_user_group_types($types) {
		$types ['vip'] = '通行证';

		return $types;
	}

	public static function getOauthVendors() {
		// 'name'=>'QQ','url'=>'',icon=>'';
		$oauth_vendors = apply_filter('get_passport_oauth_venders', array());

		return $oauth_vendors;
	}

	/**
	 *
	 * @param Query $query
	 *
	 * @return Query
	 */
	public static function on_init_autocomplete_condition_up_passport($query) {
		$query->where(array('ATABLE.type' => 'vip'));

		return $query;
	}

	/**
	 * 保存用户信息到Session时
	 *
	 * @param Passport $passport
	 * @param array    $user
	 */
	public static function on_save_user_passport_vip($passport, $user) {
		$passport->setUid($user ['mid']);
		$passport->setAvatar($user ['avatar']);
		$passport->setAttr('avatar_big', $user ['avatar_big']);
		$passport->setAttr('avatar_small', $user ['avatar_small']);
		$passport->setAttr('phone', $user ['phone']);
	}
}