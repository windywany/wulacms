<?php
class TaokeHookImpl {
	/**
	 * 添加导航菜单.
	 *
	 * @param AdminLayoutManager $layout        	
	 */
	public static function do_admin_layout($layout) {
		if (icando ( 'm:cms' )) {
			$menu = $layout->getNaviMenu ( 'site' );
			$pageMenu = new AdminNaviMenu ( 'taoke_menu', '淘宝客', 'fa-picture-o', tourl ( 'taoke', false ) );
			$pageMenu->addSubmenu ( array ('taokelist','淘宝客列表','fa-picture-o',tourl ( 'taoke', false ) ), false, 1 );
			$pageMenu->addSubmenu ( array ('addtaoke','添加淘宝客','fa-picture-o',tourl ( 'cms/page/add/page/taoke', false ) ), false, 2 );
			
			$menu->addItem ( $pageMenu, false, 15 );
		}
	}
	public static function on_load_dashboard_css($html) {
		$html .= 'td.taoke img { width:128px;height:auto;} .taoke-prev img{width:100%;height:auto;}';
		return $html;
	}
	public static function load_taoke_model() {
		return new TaokeContentModel ();
	}
	public static function get_content_list_page_url($url, $page) {
		if ($page ['model'] == 'taoke') {
			$url = tourl ( 'taoke', false );
		}
		return $url;
	}
	public static function get_extra_saved_actions($actions, $page) {
		if ($page ['model'] == 'taoke') {
			$actions [] = '[<a href="#' . tourl ( 'taoke/pic/' ) . $page ['id'] . '" onclick="nUI.closeAjaxDialog()">查看相片</a>]';
			$actions [] = '[<a href="#' . tourl ( 'taoke/upload/' ) . $page ['id'] . '" onclick="nUI.closeAjaxDialog()">上传相片</a>]';
		}
		return $actions;
	}
	public static function get_recycle_content_type($types) {
		$types ['AlbumPic'] = '相册图片';
		return $types;
	}
	public static function on_destroy_cms_page($ids) {
		dbdelete ()->from ( '{taoke}' )->where ( array ('page_id IN' => $ids ) )->exec ();
		dbdelete ()->from ( '{album_item}' )->where ( array ('album_id IN' => $ids ) )->exec ();
	}
	public static function on_destroy_album_item($ids) {
		dbdelete ()->from ( '{album_item}' )->where ( array ('id IN' => $ids ) )->exec ();
	}
	public static function on_load_page_fields($fields) {
		if ($fields ['model'] == 'taoke') {
			$router = Router::getRouter ();
			$cp = $router->getCurrentPageNo ();
			$fields ['total_pages'] = $fields ['album_items_count'];
			$fields ['content'] = $fields ['album_items'] [$cp] ['url'];
		}
		return $fields;
	}
}
