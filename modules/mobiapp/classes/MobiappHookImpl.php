<?php
class MobiappHookImpl {
	
	/**
	 *
	 * @param AdminLayoutManager $layout
	 */
	public static function do_admin_layout($layout) {
		if (icando ( 'm:mobi' )) {
			$menu = new AdminNaviMenu ( 'mobi', 'APP', 'fa-android text-success' );
			if (icando ( 'ch:mobi' )) {
				$ads = new AdminNaviMenu ( 'mobi_ch', '内容栏目', 'fa-sitemap', tourl ( 'mobiapp/channel', false ) );
				$menu->addItem ( $ads, false, 1 );
			}
			if (icando ( 'pv:mobi' )) {
				$ads = new AdminNaviMenu ( 'mobi_pv', '展示模板', 'fa-columns', tourl ( 'mobiapp/pageview', false ) );
				$menu->addItem ( $ads, false, 2 );
			}
			if (icando ( 'm:mobi/ch' )) {
				$ads = new AdminNaviMenu ( 'mobi_ps', '内容列表', 'fa-copy', tourl ( 'mobiapp/page', false ) );
				$menu->addItem ( $ads, false, 3 );
			}
			if (icando ( 'ads:mobi' )) {
				$ads = new AdminNaviMenu ( 'mobi_ads', '广告配置', 'fa-info', tourl ( 'mobiapp/ads', false ) );
				$menu->addItem ( $ads, false, 4 );
			}
			if (icando ( 'ver:mobi' )) {
				$ads = new AdminNaviMenu ( 'mobi_update', '版本列表', 'fa-download', tourl ( 'mobiapp/version', false ) );
				$menu->addItem ( $ads, false, 5 );
			}
			$layout->addNaviMenu ( $menu, 99 );
		}
		if (icando ( 'm:system/preference' )) {
			$sysMenu = $layout->getNaviMenu ( 'system' );
			$settingMenu = $sysMenu->getItem ( 'preferences' );
			$settingMenu->addSubmenu ( array ('mobiapp','移动端设置','fa-cog',tourl ( 'mobiapp/preference', false ) ), 'mobiapp:system/preference' );
		}
	}
	/**
	 *
	 * @param AclResourceManager $manager
	 * @return AclResourceManager
	 */
	public static function get_acl_resource($manager) {
		$acl = $manager->getResource ( 'mobi', '移动数据源' );
		$acl->addOperate ( 'm', '移动数据源', '', true );
		$acl->addOperate ( 'pv', '展示管理' );
		$acl->addOperate ( 'ch', '栏目管理' );
		$acl->addOperate ( 'pb', '发布内容' );
		$acl->addOperate ( 'ads', '广告方案管理' );
		$acl->addOperate ( 'ver', '版本管理' );
		$acl = $manager->getResource ( 'mobi/ch', '内容管理' );
		$acl->addOperate ( 'm', '内容管理', '', true );
		$channels = MobiChannelForm::getAllChannels ();
		foreach ( $channels as $ch ) {
			$acl->addOperate ( 'm_' . $ch ['refid'], $ch ['name'] . '栏目' );
		}
		$acl = $manager->getResource ( 'system/preference' );
		$acl->addOperate ( 'mobiapp', '移动端设置' );
		return $manager;
	}
	public static function get_recycle_content_type($types) {
		$types ['MobiChannel'] = '移动栏目';
		$types ['MobiPage'] = '移动页面';
		$types ['MobiPageView'] = '移动展示模板';
		return $types;
	}
	public static function on_destroy_mobi_channel($ids) {
		$refids = dbselect ( 'refid' )->from ( '{mobi_channel}' )->where ( array ('id IN' => $ids,'deleted' => 0 ) )->toArray ( 'refid' );
		if ($refids) {
			dbdelete ()->from ( '{mobi_channel_binds}' )->where ( array ('mobi_refid IN' => $refids ) )->exec ();
		}
	}
	public static function get_extra_saved_actions($actions, $page) {
		if (icando ( 'm:mobi/ch' )) {
			if (dbselect ()->from ( '{mobi_channel_binds}' )->where ( array ('cms_refid' => $page ['channel'] ) )->exist ( 'cms_refid' )) {
				$actions [] = '[<a href="javascript:void(0);" onclick="return MobiApp.push2mobiapp(' . $page ['id'] . ',\'\',\'\',true);">APP内容</a>]';
			}
		}
		return $actions;
	}
	public static function get_page_actions($actions, $page) {
		if (icando ( 'm:mobi/ch' )) {
			$actions .= '<li><a href="javascript:void(0);" onclick="return MobiApp.push2mobiapp(' . $page ['id'] . ');"><i class="fa fa-fw fa-android text-success"></i> APP内容</a></li>';
		}
		return $actions;
	}
	/**
	 *
	 * @param Query $query
	 * @param array $con
	 * @return Query
	 */
	public static function build_page_common_query($query, $con = array()) {
		if (isset ( $con ['ismobipage'] ) && $con ['ismobipage'] == 'on') {
			if (icando ( 'm:mobi/ch' ) && $query) {
				$sql = dbselect ( 'MCP.page_id' )->from ( '{mobi_page} AS MCP' )->where ( array ('MCP.page_id' => imv ( 'CP.id' ),'MCP.deleted' => 0 ) );
				
				$query->where ( array ('!@' => $sql ) );
			}
		}
		return $query;
	}
	public static function get_customer_cms_search_field($fields, $type) {
		if (icando ( 'm:mobi/ch' )) {
			$fields ['ismobipage'] = array ('col' => 2,'widget' => 'htmltag','defaults' => '<div class="inline-group"><label class="checkbox"><input type="checkbox" name="ismobipage"><i></i>不在移动端</label></div>' );
		}
		return $fields;
	}
	public static function on_load_dashboard_js_file($html) {
		$js = MODULE_URL . 'mobiapp/mobiapp.js';
		$html .= '<script type="text/javascript" src="' . $js . '"></script>';
		return $html;
	}
	public static function on_dashboard_window_ready_scripts($scripts) {
		$scripts .= 'window.MobiApp.URL="' . tourl ( 'mobiapp' ) . "\";\n";
		$scripts .= 'window.MobiApp.init();' . "\n";
		return $scripts;
	}
	public static function on_load_dashboard_css($css) {
		$mycss = '.mobi-box {width:100%;position:relative;} .mobi-box,.mobi-box * {padding:0;margin:0;}
				  .mobi-box ul,.mobi-box li {list-style:none;outline: 0 none;} .mobi-box li img {width:100%;cursor:pointer;}
				  .mobi-box .cursouel li{position:relative} .mobi-box .cursouel li p{text-algin:center;color:#fff;position:absolute;left:0;right:0;bottom:0;height:35px;line-height:35px;}
				  .mobi-box .cursouel li p.bak {background:#999;opacity:.80}
				  ul.threepic li {float:left;width:30%;margin:5px;}
				  ul.pictext li.pic {float:left;width:30%;margin:5px;} ul.pictext li.text {float:right;width:60%;margin:5px;} ';
		return $css . $mycss;
	}
}