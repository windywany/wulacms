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
		if (icando ( 'm:account/member' )) {
			$acc = $layout->getNaviMenu ( 'account' );
			$menu = new AdminNaviMenu ( 'memlist', '通行证', 'fa-user', tourl ( 'passport/members', false ) );
			$acc->addItem ( $menu, false );
		}
		if (icando ( 'm:system/preference' )) {
			$sysMenu = $layout->getNaviMenu ( 'system' );
			$settingMenu = $sysMenu->getItem ( 'preferences' );
			$settingMenu->addSubmenu ( array ('syspassport','通行证设置','fa-cog',tourl ( 'passport/preference', false ) ), 'pst:system/preference' );
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
	 * @return unknown
	 */
	public static function on_init_rest_server($server) {
		if (bcfg ( 'allow_remote@passport' )) {
			$server->registerClass ( new MemberRestService (), '1', 'passport.member' );
			$server->registerClass ( new PassportRestService (), '1', 'passport.admin' );
			$server->registerClass ( new UserGroupRestService (), '1', 'usergroup' );
		}
		if (cfg ( 'type@passport', 'vip' ) == 'vip') {
			$server->register ( 'passport.member.callback', '1', array (new PassportPluginImpl (),'rest_callback' ) );
		}
		return $server;
	}
	public static function get_acl_resource($manager) {
		if ('vip' == cfg ( 'type@passport' )) {
			$acl = $manager->getResource ( 'account/member', '会员管理' );
			$acl->addOperate ( 'r', '会员管理', '', true );
			$acl->addOperate ( 'u', '编辑会员' );
			$acl->addOperate ( 'd', '删除会员' );
			$acl->addOperate ( 'a', '审核会员' );
		}
		// 系统配置
		$acl = $manager->getResource ( 'system/preference', '系统配置' );
		$acl->addOperate ( 'pst', '通行证设置' );
		return $manager;
	}
	public static function get_default_apps($apps) {
		$apps ['passport'] = '通行证';
		return $apps;
	}
	/**
	 * 取登录成功后的回调接口.
	 *
	 * @param unknown $params
	 * @param unknown $key
	 * @param unknown $secret
	 * @return multitype:string
	 */
	public static function rest_callback($params, $key, $secret) {
		return array ('error' => 0,'url' => tourl ( 'passport/login', true, false ) );
	}
	public static function get_user_group_types($types) {
		$types ['vip'] = '通行证';
		return $types;
	}
	public static function getOauthVendors() {
		// 'name'=>'QQ','url'=>'',icon=>'';
		$oauth_vendors = apply_filter ( 'get_passport_oauth_venders', array () );
		return $oauth_vendors;
	}
	/**
	 *
	 * @param Query $query
	 */
	public static function on_init_autocomplete_condition_up_passport($query) {
		$query->where ( array ('ATABLE.type' => 'vip' ) );
		return $query;
	}
	/**
	 * 保存用户信息到Session时
	 *
	 * @param Passport $passport
	 * @param array $user
	 */
	public static function on_save_user_passport_vip($passport, $user) {
		$passport->setUid ( $user ['mid'] );
		$passport->setAvatar ( $user ['avatar'] );
		$passport->setAttr ( 'avatar_big', $user ['avatar_big'] );
		$passport->setAttr ( 'avatar_small', $user ['avatar_small'] );
		$passport->setAttr ( 'phone', $user ['phone'] );
	}
}