<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 注册系统权限.
 *
 * @param AclResourceManager $manager
 *        	资源管理器.
 */
function filter_for_memcached_acl_resource($manager) {
	// 系统管理
	$acl = $manager->getResource ( 'system/preference');
	$acl->addOperate ( 'cache', '缓存设置' );
	$acl = $manager->getResource ( 'system', '系统管理' );	
	$acl->addOperate ( 'cmc', '清除页面缓存' );
	return $manager;
}
