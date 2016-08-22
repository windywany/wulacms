<?php

/**
 * 火车头采集器WEB发布接口.
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
bind ( 'get_cms_preference_groups', '&LocoyHookImpl' );
bind ( 'after_save_page', '&LocoyHookImpl' );
bind ( 'alter_session_http_only', '&LocoyHookImpl', 10000 );
bind ( 'get_cms_page_status', '&LocoyHookImpl', 11 );
bind ( 'do_admin_layout', '&LocoyHookImpl' );
bind ( 'on_render_dashboard_shortcut', '&LocoyHookImpl', 100 );