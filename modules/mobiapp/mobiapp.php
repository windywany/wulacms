<?php
/*
 * 移动数据源管理.
 */
defined('KISSGO') or exit ('No direct script access allowed');

bind('do_admin_layout', '&MobiappHookImpl');
bind('get_acl_resource', '&MobiappHookImpl');

bind('on_load_dashboard_css', '&MobiappHookImpl');
bind('on_load_dashboard_js_file', '&MobiappHookImpl');
bind('on_dashboard_window_ready_scripts', '&MobiappHookImpl');

bind('get_extra_saved_actions', '&MobiappHookImpl', 20, 2);
bind('get_page_actions', '&MobiappHookImpl', 20, 2);

bind('build_page_common_query', '&MobiappHookImpl', 100, 2);

bind('get_customer_cms_search_field', '&MobiappHookImpl', 200, 2);

bind('get_recycle_content_type', '&MobiappHookImpl');
bind('on_destroy_mobi_channel', '&MobiappHookImpl');

bind('on_init_rest_server', '&MobiRestService');

register_cts_provider('mobiapp', array('mobiapp_page_provider', ksg_include('mobiapp', 'provider.php', true)), '移动数据标签', '用于调用移动页面数据.');
