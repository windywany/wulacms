<?php
class AlbumHookImpl {
	/**
	 * 添加导航菜单.
	 *
	 * @param AdminLayoutManager $layout        	
	 */
	public static function do_admin_layout($layout) {
		if (icando ( 'm:cms' )) {
			$menu = $layout->getNaviMenu ( 'site' );
			$pageMenu = new AdminNaviMenu ( 'album_menu', '相册', 'fa-picture-o', tourl ( 'album', false ) );
			$pageMenu->addSubmenu ( array ('albumlist','相册列表','fa-picture-o',tourl ( 'album', false ) ), false, 1 );
			$pageMenu->addSubmenu ( array ('addalbum','添加相册','fa-picture-o',tourl ( 'cms/page/add/page/album', false ) ), false, 2 );
			
			$menu->addItem ( $pageMenu, false, 15 );
		}
	}
	public static function on_load_dashboard_css($html) {
		$html .= 'td.album img { width:128px;height:auto;} .album-prev img{width:100%;height:auto;}';
		return $html;
	}
	public static function load_album_model() {
		return new AlbumContentModel ();
	}
	public static function get_content_list_page_url($url, $page) {
		if ($page ['model'] == 'album') {
			$url = tourl ( 'album', false );
		}
		return $url;
	}
	public static function get_extra_saved_actions($actions, $page) {
		if ($page ['model'] == 'album') {
			$actions [] = '[<a href="#' . tourl ( 'album/pic/' ) . $page ['id'] . '" onclick="nUI.closeAjaxDialog()">查看相片</a>]';
			$actions [] = '[<a href="#' . tourl ( 'album/upload/' ) . $page ['id'] . '" onclick="nUI.closeAjaxDialog()">上传相片</a>]';
		}
		return $actions;
	}
	public static function get_recycle_content_type($types) {
		$types ['AlbumPic'] = '相册图片';
		return $types;
	}
	public static function on_destroy_cms_page($ids) {
		dbdelete ()->from ( '{album}' )->where ( array ('page_id IN' => $ids ) )->exec ();
		dbdelete ()->from ( '{album_item}' )->where ( array ('album_id IN' => $ids ) )->exec ();
	}
	public static function on_destroy_album_item($ids) {
		dbdelete ()->from ( '{album_item}' )->where ( array ('id IN' => $ids ) )->exec ();
	}
	public static function on_load_page_fields($fields) {
		if ($fields ['model'] == 'album') {
			$router = Router::getRouter ();
			$cp = $router->getCurrentPageNo ();
			$fields ['total_pages'] = $fields ['album_items_count'];
			$fields ['content'] = $fields ['album_items'] [$cp] ['url'];
		}
		return $fields;
	}
}
