<?php
class WeixinHookImpl {
	/**
	 *
	 * @param AdminLayoutManager $layout        	
	 */
	public static function do_admin_layout($layout) {
		if (icando ( 'r:weixin' )) {
			$menu = new AdminNaviMenu ( 'weixin', '微信', 'fa-wechat text-success' );
			$fans = new AdminNaviMenu ( 'weixin_fans', '粉丝管理', 'fa-sitemap', tourl ( 'weixin/fans', false ));
			$ads = new AdminNaviMenu ( 'weixin_menu', '菜单管理', 'fa-sitemap', tourl ( 'weixin/menu', false ) );
			
			$msgSub = new AdminNaviMenu ( 'weixin_msg_sub', '订阅回复', 'fa-twitter', tourl ( 'weixin/message/sub', false ) );
			$msgAuto = new AdminNaviMenu ( 'weixin_msg_auto', '自动回复', 'fa-twitter', tourl ( 'weixin/message/auto', false ) );
			$msgKey = new AdminNaviMenu ( 'weixin_msg_keyword', '关键词回复', 'fa-twitter', tourl ( 'weixin/message', false ) );
			$msg = new AdminNaviMenu ( 'weixin_message', '回复管理', 'fa-twitter', tourl ( 'weixin/message', false ) );
			$msg->addItem($msgSub,'r:weixin/msg/sub', 1);
			$msg->addItem($msgAuto,'r:weixin/msg/auto', 2);
			$msg->addItem($msgKey,'r:weixin/msg/keyword', 3);
			
			$menu->addItem ( $fans, 'r:weixin/fans', 1 );
			$menu->addItem ( $ads, 'r:weixin/menu', 2 );
			$menu->addItem ( $msg, 'r:weixin/msg', 3 );
			
			$layout->addNaviMenu ( $menu, 2 );
		}
		
		if (icando ( 'm:system/preference' )) {
			$sysMenu = $layout->getNaviMenu ( 'system' );
			$settingMenu = $sysMenu->getItem ( 'preferences' );
			$settingMenu->addSubmenu ( array ('weixin','微信设置','fa-wechat text-success',tourl ( 'weixin/preference', false ) ), 'weixin:system/preference' );
		}
	}
	public static function on_destroy_weixin_account($ids) {
		dbdelete ()->from ( '{weixin_account}' )->where ( array ('id IN' => $ids ) );
	}
	public static function before_save_preference_weixin($cfgs) {
		if ($cfgs ['IsSame']) {
			$cfgs ['LoginUsername'] = $cfgs ['Username'];
			$cfgs ['LoginAppID'] = $cfgs ['AppID'];
			$cfgs ['LoginAppSecret'] = $cfgs ['AppSecret'];
			$cfgs ['LoginToken'] = $cfgs ['Token'];
		}
		return $cfgs;
	}
	/**
	 *
	 * @param AclResourceManager $manager        	
	 */
	public static function get_acl_resource($manager) {
		$acl = $manager->getResource ( 'system/preference' );
		$acl->addOperate ( 'weixin', '微信接口设置' );
		
		$acl = $manager->getResource ( 'weixin', '微信接入管理' );
		$acl->addOperate('sync', '同步粉丝');
		
		$acl = $manager->getResource ( 'weixin/channel', '自定义菜单管理' );
		$acl->addOperate ( 'r', '读取菜单', '', true );
		$acl->addOperate ( 'c', '新增菜单' );
		$acl->addOperate ( 'u', '编辑菜单' );
		$acl->addOperate ( 'd', '删除菜单' );
		return $manager;
	}
}