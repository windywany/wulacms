<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 注册系统权限.
 *
 * @param AclResourceManager $manager
 *        	资源管理器.
 */
function filter_for_media_acl_resource($manager) {
	// 系统管理
	$acl = $manager->getResource ( 'system/preference' );
	$acl->addOperate ( 'media', '多媒体设置' );
	
	$acl = $manager->getResource ( 'media', '多媒体' );
	$acl->addOperate ( 'm', '多媒体', '', true );
	$acl->addOperate ( 'upload', '上传多媒体' );
	return $manager;
}
