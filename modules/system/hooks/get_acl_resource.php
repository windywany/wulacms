<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 注册系统权限.
 *
 * @param AclResourceManager $manager
 *        	资源管理器.
 */
function filter_for_add_system_acl_resource($manager) {
	$r = $manager->getResource ( 'account', '账户管理' );
	$r->addOperate ( 'm', '账户管理', '', true );
	// 用户组管理
	$acl = $manager->getResource ( 'account/usergroup', '用户组管理' );
	$acl->addOperate ( 'r', '查看用户组', '', true );
	$acl->addOperate ( 'c', '新增用户组' );
	$acl->addOperate ( 'u', '修改用户组' );
	$acl->addOperate ( 'd', '删除用户组' );
	$acl->addOperate ( 'a', '应用到所有组' );
	// 角色管理
	$acl = $manager->getResource ( 'account/role', '角色管理' );
	$acl->addOperate ( 'r', '查看角色', '', true );
	$acl->addOperate ( 'c', '新增角色' );
	$acl->addOperate ( 'u', '修改角色' );
	$acl->addOperate ( 'd', '删除角色' );
	$acl->addOperate ( 'acl', '权限设置' );
	// 用户管理
	$acl = $manager->getResource ( 'account/user', '用户管理' );
	$acl->addOperate ( 'r', '查看用户', '', true );
	$acl->addOperate ( 'c', '新增用户' );
	$acl->addOperate ( 'u', '修改用户' );
	$acl->addOperate ( 'd', '删除用户' );
	// 插件管理
	$acl = $manager->getResource ( 'report', '报表管理' );
	$acl->addOperate ( 'r', '报表列表', '', true );
	// 插件管理
	$acl = $manager->getResource ( 'plugin', '插件管理' );
	$acl->addOperate ( 'r', '插件列表', '', true );
	$acl->addOperate ( 'i', '安装插件' );
	$acl->addOperate ( 'u', '升级插件' );
	$acl->addOperate ( 'd', '禁用插件' );
	$acl->addOperate ( 'ui', '卸载插件' );
	// 系统管理
	$acl = $manager->getResource ( 'system', '系统管理' );
	$acl->addOperate ( 'm', '系统管理', '', true );
	$acl->addOperate ( 'cc', '清除缓存' );
	$acl->addOperate ( 'cron', '运行定时任务' );
	
	$acl = $manager->getResource ( 'system/log', '系统日志' );
	$acl->addOperate ( 'r', '查看日志', '', true );
	$acl->addOperate ( 'd', '删除日志' );
	// 分类管理
	$acl = $manager->getResource ( 'system/catalog', '常量管理' );
	$acl->addOperate ( 'm', '查看', '', true );
	$acl->addOperate ( 'ct', '新增类型' );
	$acl->addOperate ( 'ut', '编辑类型' );
	$acl->addOperate ( 'dt', '删除类型' );
	$types = apply_filter ( 'get_catalog_types', array () );
	foreach ( $types as $id => $cata ) {
		$acl = $manager->getResource ( 'system/catalog/' . $id, $cata ['name'] );
		$acl->addOperate ( 'r', '查看', '', true );
		$acl->addOperate ( 'c', '新增' );
		$acl->addOperate ( 'u', '编辑' );
		$acl->addOperate ( 'd', '删除' );
	}
	// 系统配置
	$acl = $manager->getResource ( 'system/preference', '系统配置' );
	$acl->addOperate ( 'm', '系统配置', '', true );
	$acl->addOperate ( 'gm', '通用配置' );
	// 系统公告
	$acl = $manager->getResource ( 'system/notice', '系统公告' );
	$acl->addOperate ( 'r', '查看公告', '', true );
	$acl->addOperate ( 'c', '发布公告' );
	$acl->addOperate ( 'u', '编辑公告' );
	$acl->addOperate ( 'd', '删除公告' );
	// 回收站
	$acl = $manager->getResource ( 'recycle', '回收站' );
	$acl->addOperate ( 'm', '回收站', '', true );
	$acl->addOperate ( 'u', '还原' );
	$acl->addOperate ( 'd', '清空' );
	return $manager;
}
