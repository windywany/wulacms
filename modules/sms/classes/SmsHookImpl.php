<?php
class SmsHookImpl {
	/**
	 *
	 * @param AdminLayoutManager $layout        	
	 */
	public static function do_admin_layout($layout) {
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
		return $manager;
	}
}
