<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
bind ( 'init_smarty_engine', 'hook_for_dashboard_init_smarty_engine' );
function hook_for_dashboard_init_smarty_engine($smarty) {
	$data ['SiteURL'] = cfg ( 'site_url', DETECTED_ABS_URL );
	$data ['AppURL'] = DETECTED_ABS_URL;
	$data ['validateURL'] = AbstractForm::getValidateUrl ();
	$data ['HomeURL'] = cfg ( 'cms_url@cms', DETECTED_ABS_URL );
	$data ['siteurl'] = $data ['SiteURL'];
	$data ['SiteName'] = cfg ( 'site_name' );
	$data ['sub_domain'] = CUR_SUBDOMAIN;
	$data ['KISS_VERSION'] = KISS_VERSION;
	$data ['KISS_RELEASE_VER'] = KISS_RELEASE_VER;
	$data ['KISS_BUILD_ID'] = KISS_BUILD_ID;
	$data ['KISS_NAME'] = KISS_NAME;
	$data ['LANGS'] = get_system_support_langs ();
	$data ['KISS_START_TIME'] = KIS_START_TIME;
	$data ['_CFG'] = cfg ( null );
	$agent = $_SERVER ['HTTP_USER_AGENT'];
	if (stripos ( $agent, 'iPhone' )) {
		$data ['HTTP_USER_AGENT'] = 'iphone';
	} else if (stripos ( $agent, 'Android' )) {
		$data ['HTTP_USER_AGENT'] = 'android';
	} else if (stripos ( $agent, 'ipad' )) {
		$data ['HTTP_USER_AGENT'] = 'ipad';
	} else {
		$data ['HTTP_USER_AGENT'] = 'pc';
	}
	$smarty->assign ( $data );
	return $smarty;
}
/**
 * 创建一个smart button group.
 *
 * @return HtmlTagElm
 */
function smart_btn_group() {
	$group = dashboard_htmltag ( 'div' )->cls ( 'btn-group' );
	return $group;
}
/**
 * 创建一个smart button.
 *
 * @param string $text
 * @param string $icon
 * @param string $theme
 * @param string $tag
 * @return HtmlTagElm
 */
function smart_btn($text = '', $icon = '', $theme = 'btn-default', $tag = 'button') {
	$btn = dashboard_htmltag ( $tag )->cls ( 'btn ' . $theme );
	if ($text) {
		$btn->text ( $text, 'true' );
	}
	if ($icon) {
		$i = dashboard_htmltag ( 'i' )->cls ( $icon );
		$btn->child ( $i );
	}
	return $btn;
}