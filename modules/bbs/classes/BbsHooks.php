<?php
namespace bbs\classes;

class BbsHooks {

    /**
     *
     * @param \AdminLayoutManager $layout
     */
    public static function do_admin_layout($layout) {
        if (! icando ( 'm:bbs' )) {
            return;
        }
        $menu = new \AdminNaviMenu ( 'bbs', 'BBS', 'fa-comments' );
        
        if (icando ( 'r:bbs/forum' )) {
            $menu->addSubmenu ( array ('bbs_forum','论坛版块','fa-sitemap',tourl ( 'bbs/forum', false ) ), false, 1 );
        }
        
        $layout->addNaviMenu ( $menu, 1 );
    }

    /**
     *
     * @param \AclResourceManager $manager
     * @return unknown
     */
    public static function get_acl_resource($manager) {
        $acl = $manager->getResource ( 'bbs', '论坛管理' );
        $acl->addOperate ( 'm', '论坛管理', true );
        $acl->addOperate ( 'cfg', '论坛设置' );
        $acl = $manager->getResource ( 'bbs/forum', '版块管理' );
        $acl->addOperate ( 'r', '版块管理', '', true );
        $acl->addOperate ( 'c', '新增版块' );
        $acl->addOperate ( 'u', '编辑版块' );
        $acl->addOperate ( 'd', '删除版块' );
        
        $acl = $manager->getResource ( 'bbs/thread', '帖子管理' );
        $acl->addOperate ( 'r', '帖子管理', '', true );
        $acl->addOperate ( 'u', '编辑帖子' );
        $acl->addOperate ( 'd', '删除帖子' );
        $acl->addOperate ( 'cls', '关闭帖子' );
        $acl->addOperate ( 'type', '帖子类型设置' );
        
        $acl = $manager->getResource ( 'bbs/post', '回复管理' );
        $acl->addOperate ( 'r', '回复管理', '', true );
        $acl->addOperate ( 'u', '编辑回复' );
        $acl->addOperate ( 'd', '删除回复' );
        
        $acl = $manager->getResource ( 'bbs/rank', '级别管理' );
        $acl->addOperate ( 'r', '级别管理', '', true );
        $acl->addOperate ( 'c', '新增级别' );
        $acl->addOperate ( 'u', '编辑级别' );
        $acl->addOperate ( 'd', '删除级别' );
        return $manager;
    }

    public static function get_recycle_content_type($types) {
        return $types;
    }

    public static function on_render_navi_btns($btns) {
        if (icando ( 'r:bbs/forum' )) {
            $btns .= '<div class="btn-header transparent pull-right">
    			<span>
    				<a href="#' . tourl ( 'bbs/forum', false ) . '" title="论坛版块">
    					<i class="fa fa-fw fa-comments"></i>
    				</a>
    			</span>
    		</div>';
        }
        return $btns;
    }
}
