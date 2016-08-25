<?php
class LocoyHookImpl {
	public static function get_cms_preference_groups($groups) {
		$groups ['locoy'] = array ('name' => '火车头','form' => 'LocoyPreferenceForm','group' => 'locoy','icon' => 'fa-truck' );
		return $groups;
	}
	public static function after_save_page($page) {
		if (rqset ( '_locoy' )) {
			$status = icfg ( 'status@locoy', 8 );
			if ($status == 8) {
				$data ['url'] = '';
				$data ['url_key'] = '';
			}
			$data ['status'] = $status;
			// 发布或待发布且发布时间为空,则使用当前时间做为发布时间.
			if (in_array ( $status ['status'], array (2,4 ) ) && ! isset ( $page ['publish_time'] )) {
				$data ['publish_time'] = time ();
			}
			dbupdate ( '{cms_page}' )->set ( $data )->where ( array ('id' => $page ['id'] ) )->exec ();
		}
		return $page;
	}
	/**
	 *
	 * @param AdminLayoutManager $layout        	
	 */
	public static function do_admin_layout($layout) {
		if (icando ( 'm:cms' ) && icando ( 'u:cms/page' )) {
			$menu = $layout->getNaviMenu ( 'site' );
			$modelMenu = new AdminNaviMenu ( 'accept_page', '入库', 'fa-check-square-o txt-color-green', tourl ( 'cms/page', false ) . '?status=8' );
			$menu->addItem ( $modelMenu, false, 51 );
		}
		
		return $layout;
	}
	public static function on_render_dashboard_shortcut($shortcut) {
		if (icando ( 'u:cms/page' )) {
			$shortcut .= '<li>
				<a class="jarvismetro-tile big-cubes bg-color-green" href="#' . tourl ( 'cms/page', false ) . '?status=8">
					<span class="iconbox">
						<i class="fa fa-check-square-o fa-5x"></i>
						<span class="text-center">采集入库</span>
					</span>
				</a>
			</li>';
		}
		return $shortcut;
	}
	public static function alter_session_http_only($only) {
		return ! bcfg ( 'locoy_enabled@locoy' );
	}
	public static function get_cms_page_status($status) {
		$status [8] = '待入库';
		return $status;
	}
}