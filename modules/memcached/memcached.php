<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
$settings = KissGoSetting::getSetting ();
if (isset ( $settings ['cache_prefix'] )) {
	define ( 'MCACHE_PREFIX', $settings ['cache_prefix'] );
} else {
	define ( 'MCACHE_PREFIX', WEB_ROOT );
}

if (isset ( $settings ['page_cache_prefix'] )) {
	define ( 'PCACHE_PREFIX', $settings ['page_cache_prefix'] );
} else {
	define ( 'PCACHE_PREFIX', 'page' );
}
if (isset ( $settings ['cnt_cache_prefix'] )) {
	define ( 'NCACHE_PREFIX', $settings ['cnt_cache_prefix'] );
} else {
	define ( 'NCACHE_PREFIX', 'cnt' );
}
if (isset ( $settings ['block_cache_prefix'] )) {
	define ( 'BCACHE_PREFIX', $settings ['block_cache_prefix'] );
} else {
	define ( 'BCACHE_PREFIX', 'block' );
}
if (isset ( $settings ['chunk_cache_prefix'] )) {
	define ( 'CCACHE_PREFIX', $settings ['chunk_cache_prefix'] );
} else {
	define ( 'CCACHE_PREFIX', 'chunk' );
}
bind ( 'do_admin_layout', 'hook_for_do_admin_layout_memcached@hooks/do_admin_layout' );
bind ( 'on_render_navi_btns', 'hook_for_on_render_navi_btns_page_mm@hooks/do_admin_layout' );
bind ( 'on_init_dynamicform_MemPreferenceForm', array ('MemPreferenceBaseForm','init' ) );
bind ( 'get_cache_manager', 'hook_for_get_memcached_cache_manager@hooks/memcached' );
bind ( 'get_acl_resource', 'filter_for_memcached_acl_resource@hooks/get_acl_resource' );
bind ( 'show_system_info', 'hook_for_show_system_info_memcache@hooks/memcached' );
bind ( 'on_render_homepage', 'hook_for_on_render_homepage_memcache@hooks/memcached' );