<?php
/*
 * KissCms
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
// 注册RBAC驱动器
bind ( 'get_rbac_driver', 'hook_for_getrbacdriver_system_impl', 100, 2 );
// 注册布局HOOK
bind ( 'do_admin_layout', 'hook_for_do_admin_layout_system@hooks/do_admin_layout', 1 );
// 注册权限HOOK
bind ( 'get_acl_resource', 'filter_for_add_system_acl_resource@hooks/get_acl_resource' );
// 注册 PreferenceForm HOOK
bind ( 'on_init_dynamicform_PreferenceForm', array ('PreferenceBaseForm','init' ) );
bind ( 'on_init_dynamicform_PassportClientPreferenceForm', array ('PassportClientPreferenceForm','init' ) );
bind ( 'get_activity_log_type', 'hook_for_activity_types_system' );
bind ( 'get_recycle_content_type', 'hook_for_recycle_type_system' );
bind ( 'get_custom_field_widgets', '&AutoCompleteWidget' );
bind ( 'on_init_autocomplete_condition_user', '&AutoCompleteWidget' );
bind ( 'get_custom_field_widgets', '&CatalogSelectWidget' );
bind ( 'on_init_rest_server', '&CatalogForm' );
bind ( 'get_catalog_types', '&CatatypeForm', 99999, 2 );
bind ( 'get_model_link_groups', '&CatatypeForm', 9999 );
bind ( 'get_widget_views', 'widget_views_from_dashboard' );
bind ( 'get_widget_data_providors', 'widget_data_providors_from_dashboard' );
bind ( 'init_smarty_engine', 'init_global_smarty_engine' );
register_cts_provider ( 'catalog', array ('system_catalog_provider',ksg_include ( 'system', 'providers/system_providers.php', true ) ), '分类调用标签', '调用分类数据.', true );
/**
 * 用户权限校验.
 *
 * @param unknown $driver        	
 * @return AclRbacDriver
 */
function hook_for_getrbacdriver_system_impl($driver, $type) {
	if ($driver == null && $type == 'admin') {
		$driver = new AclRbacDriver ();
	}
	return $driver;
}
function hook_for_activity_types_system($types) {
	$types ['Syslog'] = __ ( '系统日志' );
	$types ['Delete Activity'] = __ ( '删除日志' );
	$types ['Uninstall Plugin'] = __ ( '卸载插件' );
	$types ['Install Plugin'] = __ ( '安装插件' );
	$types ['Update Plugin'] = __ ( '更新插件' );
	$types ['Enable Plugin'] = __ ( '雇用插件' );
	$types ['Disable Plugin'] = __ ( '禁用插件' );
	
	return $types;
}
function hook_for_recycle_type_system($types) {
	$types ['User'] = __ ( '管理员' );
	$types ['Group'] = __ ( '账户组' );
	$types ['Role'] = __ ( '角色' );
	$types ['Notice'] = __ ( '通知' );
	return $types;
}
function widget_views_from_dashboard($views) {
	$views ['TwoLevelULNavigator'] = '基于ul标签的二级菜单';
	return $views;
}
function widget_data_providors_from_dashboard($p) {
	$p ['HookArrayDataProvidor'] = '插件数组数据源';
	$p ['HookStringDataProvidor'] = '插件文本数据源';
	return $p;
}
function get_system_support_langs() {
	static $lang_list = false;
	if ($lang_list === false) {
		$lang_list = array ();
		$langs = cfg ( 'langs' );
		if ($langs) {
			$tpl = cfg ( 'theme', 'default' );
			$mtpl = cfg ( 'mobi_theme', 'default' );
			$langs = explode ( "\n", trim ( $langs ) );
			foreach ( $langs as $lang ) {
				@list ( $lid, $lname, $tpl1, $mtpl1 ) = explode ( ';', $lang );
				$lang_list [$lid] = array ('label' => $lname,'theme' => $tpl1 ? $tpl1 : $tpl,'mobi_theme' => $mtpl1 ? $mtpl1 : $mtpl );
			}
		}
	}
	return $lang_list;
}
/**
 *
 * @param Smarty $smarty        	
 */
function init_global_smarty_engine($smarty) {
	class_exists ( 'Smarty_Internal_Compile_Ican' );
	$router = Router::getRouter ();
	$args = $router->getRequestArgs ();
	$smarty->assign ( 'ARGS', $args, true );
	return $smarty;
}
function smarty_function_catalog($params, $template) {
	if (empty ( $params ['var'] )) {
		trigger_error ( "[plugin] catalog parameter 'var' cannot be empty", E_USER_NOTICE );
		return;
	}
	if (! isset ( $params ['id'] )) {
		trigger_error ( "[plugin] catalog parameter 'id' cannot be empty", E_USER_NOTICE );
		$template->assign ( $params ['var'], array () );
		return;
	}
	
	$id = explode ( ',', $params ['id'] );
	$id = intval ( $id [0] );
	if (empty ( $id )) {
		$template->assign ( $params ['var'], array () );
		return;
	}
	
	$catalog = dbselect ( '*' )->from ( '{catalog}' )->where ( array ('id' => $id ) );
	$catalog = $catalog->get ();
	if ($catalog) {
		$template->assign ( $params ['var'], $catalog );
	} else {
		$template->assign ( $params ['var'], array () );
	}
}
function smarty_modifiercompiler_catalogname($params, $compiler) {
	$ids = $params [0];
	$c = "','";
	$n = 'null';
	if (isset ( $params [1] )) {
		$c = $params [1];
	}
	if(isset($params[2])){
		$n = $params[2];
	}
	return 'CatalogForm::cataname(' . $params [0] . ',' . $c . ','.$n.')';
}
// end of system.php