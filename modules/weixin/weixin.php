<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

bind ( 'do_admin_layout', '&WeixinHookImpl' );

bind ( 'get_acl_resource', '&WeixinHookImpl' );

bind ( 'on_weixin_event_subscribe', '&\weixin\classes\WeixinEventHandler', 1, 3 );
bind ( 'on_weixin_event_unsubscribe', '&\weixin\classes\WeixinEventHandler', 1, 3 );

bind ( 'on_weixin_message_text', '&\weixin\classes\WeixinMsgHandler', 1, 3 );


bind ( 'before_save_preference_weixin', '&WeixinHookImpl' );

bind ( 'crontab', '&\weixin\classes\WeixinCrontab' );

bind ( 'on_init_rest_server', '&WeixinRestService' );
/**
 * 过滤微信响应中的换行,回车等能够导致json_decode无法正常解析字符.
 *
 * @param string $content
 *        	微信响应.
 * @return string 过滤后的字符.
 */

function weixin_response_filter($content) {
	$content = str_replace ( array ("\n","\r","\\n" ), '', $content );
	return $content;
}
/**
 * 生成微信tree.
 *
 * @param unknown $params        	
 * @param unknown $template        	
 */
function smarty_function_weixin_tree($params, $template) {
	if (empty ( $params ['name'] )) {
		trigger_error ( "[plugin] page parameter 'name' cannot be empty", E_USER_NOTICE );
		return;
	}
	$name = $params ['name'];
	if (empty ( $params ['id'] )) {
		$id = $name;
	} else {
		$id = $params ['id'];
	}
	if (empty ( $params ['value'] )) {
		$value = '';
	} else {
		$value = $params ['value'];
	}
	$type = 0;
	if (! empty ( $params ['type'] )) {
		$type = 1;
	}
	if (! empty ( $params ['placeholder'] )) {
		$placeholder = $params ['placeholder'];
	}
	if (! empty ( $params ['cid'] )) {
		$defaults ['cid'] = $params ['cid'];
	}
	if (! empty ( $params ['multi'] )) {
		$defaults ['multi'] = $params ['multi'];
	}
	$defaults ['table'] = 'weixin_menu';
	if ($value) {
		$defaults ['cid'] = $value;
	}
	$field = array ('name' => $name,'id' => $id,'value' => $value,'placeholder' => $placeholder,'widget' => 'treeview','defaults' => json_encode ( $defaults ) );
	
	$widget = CustomeFieldWidgetRegister::initWidgets ( array ($name => $field ), array ($name => $value ) );
	if ($widget) {
		echo $widget [$name] ['widget']->render ( $widget [$name] );
	}
}
