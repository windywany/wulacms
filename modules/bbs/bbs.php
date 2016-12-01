<?php
bind ( 'do_admin_layout', '&bbs\classes\BbsHooks' );
bind ( 'get_acl_resource', '&bbs\classes\BbsHooks' );
bind ( 'get_recycle_content_type', '&bbs\classes\BbsHooks' );
bind ( 'on_render_navi_btns', '&bbs\classes\BbsHooks' );
if (bcfg ( 'enable_short@cms', true )) {
	bind ( 'on_render_dashboard_shortcut', '&bbs\classes\BbsHooks' );
}
register_cts_provider('forums',new \bbs\classes\ForumDataProvider(),'论坛版块列表','通过条件调取版块列表数据');