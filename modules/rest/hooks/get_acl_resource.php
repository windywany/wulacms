<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 注册系统权限.
 *
 * @param AclResourceManager $manager
 *        	资源管理器.
 */
function filter_for_rest_acl_resource($manager) {
	// 系统管理
	$acl = $manager->getResource ( 'system' );
	$acl->addOperate ( 'rest', '应用接入管理' );
	// 系统配置
	$acl = $manager->getResource ( 'system/preference', '系统配置' );
	$acl->addOperate ( 'rest', '应用中心设置' );
	return $manager;
}
