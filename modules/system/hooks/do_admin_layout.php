<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 注册导航菜单.
 *
 * @param AdminLayoutManager $layout
 */
function hook_for_do_admin_layout_system($layout) {
	if (icando ( 'r:report' )) {
		$reportMenu = new AdminNaviMenu ( 'report', '报表', 'fa-bar-chart-o' );
		fire ( 'on_init_report_menu', $reportMenu );
		if ($reportMenu->hasSubMenu ()) {
			$layout->addNaviMenu ( $reportMenu, 99997 );
		}
	}
	if (icando ( 'm:account' )) {
		// 账户菜单
		$accountMenu = new AdminNaviMenu ( 'account', '账户', 'fa-group' );
		$accountMenu->addSubmenu ( array ('usergroup','账户分组','fa-group',tourl ( 'system/group', false ) ), 'r:account/usergroup', 0 );
		$accountMenu->addSubmenu ( array ('role','账户角色','fa-user-md',tourl ( 'system/role', false ) ), 'r:account/role', 1 );
		$accountMenu->addSubmenu ( array ('user','管理账户','fa-male',tourl ( 'system/user', false ) ), 'r:account/user', 2 );
		$layout->addNaviMenu ( $accountMenu, 99999 );
	}
	
	if (icando ( 'm:system' )) {
		// 系统菜单
		$sysMenu = new AdminNaviMenu ( 'system', '系统', 'fa-gears' );
		
		$sysMenu->addSubmenu ( array ('syslog','系统日志','fa-book',tourl ( 'system/syslog', false ) ), 'r:system/log', 0 );
		if (icando ( 'm:plugin' )) {
			// 插件菜单
			$pluginMenu = new AdminNaviMenu ( 'plugin', '插件', 'fa-puzzle-piece' );
			$apps = AppInstaller::getApps ();
			$cnt = 0;
			foreach ( $apps as $app ) {
				if ($app ['upgradable']) {
					$cnt ++;
				}
			}
			$tip = '';
			if ($cnt > 0) {
				$pluginMenu->setTipCount ( $cnt );
				$tip = '<span class="badge inbox-badge bg-color-red pull-right">' . $cnt . '</span>';
			}
			$pluginMenu->addSubmenu ( array ('iplugins','已安装' . $tip,'fa-check-circle-o',tourl ( 'system/plugin/installed', false ) ), 'm:plugin', 0 );
			$pluginMenu->addSubmenu ( array ('uplugins','未安装','fa-circle-o',tourl ( 'system/plugin/uninstalled', false ) ), 'm:plugin', 1 );
			$sysMenu->addItem ( $pluginMenu, 'm:plugin', 99995 );
		}
		if (icando ( 'm:system/catalog' )) {
			$catelogMenu = new AdminNaviMenu ( 'catalog_menu', '数据', 'fa-database' );
			$catelogMenu->addSubmenu ( array ('cata_type_m','数据项','fa-list-ul',tourl ( 'system/catatype/', false ) ), false, 1 );
			$catelogTypes = apply_filter ( 'get_catalog_types', array (), false );
			if ($catelogTypes) {
				$catalogListMenu = new AdminNaviMenu ( 'catalist_menu', '自定义数据', 'fa-sitemap' );
				$catelogMenu->addItem ( $catalogListMenu, false, 2 );
				$i = 0;
				foreach ( $catelogTypes as $key => $val ) {
					$type = $key;
					$name = $val ['name'];
					if (isset ( $val ['nav'] ) && $val ['nav'] === false) {
						continue;
					}
					$url = tourl ( 'system/catalog/' . $type, false );
					$catalogListMenu->addSubmenu ( array ($type . '_catelog',$name,'',$url ), 'r:system/catalog/' . $type, $i ++ );
				}
			}
			$sysMenu->addItem ( $catelogMenu, 'm:system/catalog', 99996 );
		}
		
		$settingMenu = new AdminNaviMenu ( 'preferences', '系统设置', 'fa-cog' );
		$settingMenu->addSubmenu ( array ('baseSettting','通用设置','fa-cog',tourl ( 'system/preference', false ) ), 'gm:system/preference', 0 );
		$settingMenu->addSubmenu ( array ('customSettting','自定义全局设置','fa-cog',tourl ( 'system/cpreference', false ) ), 'gm:system/preference', 1 );
		
		$sysMenu->addItem ( $settingMenu, 'm:system/preference', 99998 );
		
		$cacheMenu = new AdminNaviMenu ( 'syscache', '缓存管理', 'fa-inbox' );
		$cacheMenu->addSubmenu ( array ('clear_tpl_cache','清空模板缓存','fa-eraser',tourl ( 'system/cleartpl' ),'',array ('target' => 'ajax','data-confirm' => '你真的要清空模板缓存吗?' ) ), 'cc:system', 199997 );
		$cacheMenu->addSubmenu ( array ('clear_rt_cache','清空运行时缓存','fa-eraser',tourl ( 'system/clearinner' ),'',array ('target' => 'ajax','data-confirm' => '你真的要清空运行时缓存吗?' ) ), 'cc:system', 199998 );
		$sysMenu->addItem ( $cacheMenu, 'cc:system', 99999 );
		
		$sysMenu->addSubmenu ( array ('sysnotice','系统公告','fa-volume-up',tourl ( 'system/notice', false ) ), 'cron:system', 199998 );
		$sysMenu->addSubmenu ( array ('rest_cronjob','运行定时任务','fa-refresh',tourl ( 'system/restcron' ),'',array ('target' => 'ajax','data-confirm' => '你真的要手动运行定时任务吗?' ) ), 'cron:system', 199999 );
		
		$layout->addNaviMenu ( $sysMenu, 1000000 );
	}
	if (icando ( 'm:recycle' )) {
		$trashMenu = new AdminNaviMenu ( 'recycle_menu', '回收站', 'fa-trash-o', tourl ( 'system/recycle', false ) );
		$layout->addNaviMenu ( $trashMenu, 9000000 );
	}
	$layout->addUserProfileLink ( '个人设置', tourl ( 'system/user/profile', false ), 'fa-user' );
}
