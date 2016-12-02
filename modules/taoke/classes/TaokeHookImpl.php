<?php

namespace taoke\classes;

class TaokeHookImpl {
	/**
	 * 添加导航菜单.
	 *
	 * @param \AdminLayoutManager $layout
	 */
	public static function do_admin_layout($layout) {
		if (icando('m:cms')) {
			$menu     = $layout->getNaviMenu('site');
			$pageMenu = new \AdminNaviMenu ('taoke_menu', '淘宝客', 'fa-picture-o', tourl('taoke', false));
			$pageMenu->addSubmenu(array('taokelist', '淘宝客列表', 'fa-picture-o', tourl('taoke', false)), false, 1);
			$pageMenu->addSubmenu(array('addtaoke', '生成淘口令', 'fa-picture-o', tourl('taoke/generate', false)), false, 2);
			$pageMenu->addSubmenu(array('config', '淘宝客配置', 'fa-picture-o', tourl('taoke/preference', false)), false, 2);
			$menu->addItem($pageMenu, false, 15);
		}
	}

	public static function load_taoke_model($model = null) {
		return new TaokeContentModel();
	}

	public static function get_content_list_page_url($url, $page) {
		if ($page ['model'] == 'taoke') {
			$url = tourl('taoke', false);
		}

		return $url;
	}

	public static function on_destroy_cms_page($ids) {
		dbdelete()->from('{tbk_goods}')->where(array('page_id IN' => $ids))->exec();
	}
}
