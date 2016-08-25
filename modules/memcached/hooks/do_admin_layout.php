<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
function hook_for_do_admin_layout_memcached($layout) {
	// 完成网站菜单配置
	if (icando ( 'm:system/preference' )) {
		$sysMenu = $layout->getNaviMenu ( 'system' );
		$settingMenu = $sysMenu->getItem ( 'preferences' );
		$settingMenu->addSubmenu ( array ('cacheSettting','缓存设置','fa-cog',tourl ( 'memcached', false ) ), 'cache:system/preference' );
	}
	if (icando ( 'cc:system' ) && icando ( 'cmc:system' )) {
		$sysMenu = $layout->getNaviMenu ( 'system' );
		$cacheMenu = $sysMenu->getItem ( 'syscache' );
		$cacheMenu->addSubmenu ( array ('clearacache','清空全部缓存','fa-eraser',tourl ( 'memcached/clear' ),'',array ('target' => 'ajax','data-confirm' => '你真的要清空页面缓存吗?' ) ), false, 1 );
		$cacheMenu->addSubmenu ( array ('clearpcache','清空页面缓存','fa-eraser',tourl ( 'memcached/clear/page' ),'',array ('target' => 'ajax','data-confirm' => '你真的要清空页面缓存吗?' ) ), false, 2 );
		$cacheMenu->addSubmenu ( array ('clearbcache','清空内容缓存','fa-eraser',tourl ( 'memcached/clear/cnt' ),'',array ('target' => 'ajax','data-confirm' => '你真的要清空内容缓存吗?' ) ), false, 3 );
		$cacheMenu->addSubmenu ( array ('clearbcache','清空区块缓存','fa-eraser',tourl ( 'memcached/clear/block' ),'',array ('target' => 'ajax','data-confirm' => '你真的要清空区别缓存吗?' ) ), false, 4 );
		$cacheMenu->addSubmenu ( array ('clearccache','清空碎片缓存','fa-eraser',tourl ( 'memcached/clear/chunk' ),'',array ('target' => 'ajax','data-confirm' => '你真的要清空碎片缓存吗?' ) ), false, 5 );
	}
}
function hook_for_on_render_navi_btns_page_mm($btns) {
	if (icando ( 'cc:system' ) && icando ( 'cmc:system' )) {
		$btns .= '<div class="btn-header transparent pull-right">
    			<span>
    				<a href="' . tourl ( 'memcached/clear' ) . '" title="清空缓存" target="ajax" data-confirm="你真的要清空全部缓存吗？">
    					<i class="fa fa-fw fa-eraser"></i>
    				</a>
    			</span>
    		</div>';
	}
	return $btns;
}