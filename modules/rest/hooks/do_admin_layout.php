<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 注册导航菜单.
 *
 * @param AdminLayoutManager $layout        	
 */
function hook_for_do_admin_layout_rest($layout) {
	if (! bcfg ( 'connect_server@rest', false ) && icando ( 'rest:system' ) && bcfg ( 'allow_remote@rest' )) {
		$sysMenu = $layout->getNaviMenu ( 'system' );
		$sysMenu->addSubmenu ( array ('rest','应用接入','fa-puzzle-piece',tourl ( 'rest/app', false ) ), 'rest:system', 0 );
	}
	if (icando ( 'm:system/preference' )) {
		$sysMenu = $layout->getNaviMenu ( 'system' );
		$settingMenu = $sysMenu->getItem ( 'preferences' );
		$settingMenu->addSubmenu ( array ('restapps','应用中心设置','fa-cog',tourl ( 'rest/preference', false ) ), 'rest:system/preference' );
	}
}
function hook_for_activity_types_rest($types) {
	$types ['Rest App'] = __ ( 'Rest App' );
	return $types;
}