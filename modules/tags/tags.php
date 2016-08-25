<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

bind ( 'get_common_page_condition_fields', '&TagsHookImpl' );
bind ( 'build_page_common_query', '&TagsHookImpl', 100, 2 );
bind ( 'load_page_common_fields', '&TagsHookImpl' );
bind ( 'save_page_common_data', '&TagsHookImpl' );
bind ( 'load_page_common_data', '&TagsHookImpl' );
bind ( 'show_page_detail', '&TagsHookImpl', 10, 2 );
bind ( 'get_customer_cms_search_field', '&TagsHookImpl', 10, 2 );
bind ( 'on_init_dynamicform_AdminPreferenceForm', '&TagsHookImpl', 100 );
bind ( 'get_page_data', '&TagsHookImpl', 100, 2 );
function smarty_modifiercompiler_tagurl($params, $compiler) {
	$tag = $params [0];
	return "tags_get_tag_url($tag)";
}
function tags_get_tag_url($tag) {
	static $cache = false;
	if (bcfg ( 'usetag@cms', true )) {
		if ($cache === false) {
			$cache = Cache::getCache ();
		}
		$key = 'tag_' . md5 ( $tag );
		$url = $cache->get ( $key );
		if (is_null ( $url )) {
			$url = dbselect ( 'url' )->from ( '{cms_tag}' )->where ( array ('deleted' => 0,'tag' => $tag ) )->get ( 'url' );
			if ($url) {
				$cache->add ( $key, $url );
			} else {
				$cache->add ( $key, false );
			}
		}
		if ($url) {
			return safe_url ( $url, true );
		}
	}
	return Router::url ( 'tags' ) . urlencode ( $tag );
}