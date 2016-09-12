<?php
class SmsHookImpl {
	/**
	 *
	 * @param AdminLayoutManager $layout        	
	 */
	public static function do_admin_layout($layout) {
		if(icando('r:sms')){
			$menu = $layout->getNaviMenu('system');
			$menu->addSubmenu ( array ('smslog','短信日志','fa-twitter',tourl ( 'sms', false ) ), false, 1 );
		}
		if (icando ( 'm:system/preference' )) {
			$sysMenu = $layout->getNaviMenu ( 'system' );
			$settingMenu = $sysMenu->getItem ( 'preferences' );
			$settingMenu->addSubmenu ( array ('smspreference','短信通道设置','fa-paper-plane-o',tourl ( 'sms/preference', false ) ), 'sms:system/preference' );
		}
	}
	/**
	 *
	 * @param AclResourceManager $manager        	
	 */
	public static function get_acl_resource($manager) {
		$acl = $manager->getResource ( 'system/preference' );
		$acl->addOperate ( 'sms', '短信设置' );
		$acl = $manager->getResource ( 'sms','短信记录' );
		$acl->addOperate ( 'r', '列表', '', true );
		return $manager;
	}
}
