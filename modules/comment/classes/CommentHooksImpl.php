<?php
class CommentHooksImpl {
	public static $STATUS = array ('0' => '待审','1' => '获准','2' => '垃圾' );
	public static $MSG_STATUS = array ('0' => '待处理','1' => '已处理','2' => '垃圾','3' => '处理中' );
	/**
	 *
	 * @param AdminLayoutManager $layout        	
	 */
	public static function do_admin_layout($layout) {
		if (icando ( 'm:cms' )) {
			if (icando ( 'm:comment' )) {
				$cmsMenu = $layout->getNaviMenu ( 'site' );
				$modelMenu = new AdminNaviMenu ( 'comment_cmt', '评论', 'fa-comments-o', tourl ( 'comment', false ) );
				$cmsMenu->addItem ( $modelMenu, false, 20 );
			}
		}
		if (icando ( 'm:comment' )) {
			$modelMenu = new AdminNaviMenu ( 'comment_msg', '留言', 'fa-envelope-o' );
			$layout->addNaviMenu ( $modelMenu, 100 );
			$menu = new AdminNaviMenu ( 'comment_msg1', '最新留言', 'fa-envelope-o', tourl ( 'comment/msg', false ) );
			$modelMenu->addItem ( $menu, false, 0 );
			$menu = new AdminNaviMenu ( 'comment_msg2', '已处理留言', 'fa-envelope', tourl ( 'comment/msg/1', false ) );
			$modelMenu->addItem ( $menu, false, 1 );
		}
		
		if (icando ( 'm:system/preference' )) {
			$sysMenu = $layout->getNaviMenu ( 'system' );
			$settingMenu = $sysMenu->getItem ( 'preferences' );
			$settingMenu->addSubmenu ( array ('syscmt','评论&留言设置','fa-cog',tourl ( 'comment/preference', false ) ), 'cmt:system/preference' );
		}
	}
	
	/**
	 *
	 * @param AclResourceManager $manager        	
	 */
	public static function get_acl_resource($manager) {
		// 系统配置
		$acl = $manager->getResource ( 'system/preference', '系统配置' );
		$acl->addOperate ( 'cmt', '评论&留言设置' );
		
		$acl = $manager->getResource ( 'comment', '评论&留言' );
		$acl->addOperate ( 'm', '管理', '', true );
		$acl->addOperate ( 'u', '修改' );
		$acl->addOperate ( 'reply', '回复' );
		$acl->addOperate ( 'd', '删除' );
		$acl->addOperate ( 'a', '审核' );
		
		return $manager;
	}
	public static function get_recycle_content_type($types) {
		$types ['Comment'] = __ ( '评论' );
		return $types;
	}
}
