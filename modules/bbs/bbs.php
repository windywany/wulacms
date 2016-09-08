<?php
bind ( 'get_cms_url_handlers', '&bbs\classes\BbsHooks' );
bind ( 'do_admin_layout', '&bbs\classes\BbsHooks' );
bind ( 'get_acl_resource', '&bbs\classes\BbsHooks' );
bind ( 'get_recycle_content_type', '&bbs\classes\BbsHooks' );
bind ( 'on_render_navi_btns', '&bbs\classes\BbsHooks' );
bind ('on_destroy_bbs_forums','&bbs\model\BbsForumsModel');