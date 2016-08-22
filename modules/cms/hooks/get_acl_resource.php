<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 注册系统权限.
 *
 * @param AclResourceManager $manager
 *        	资源管理器.
 */
function filter_for_cms_acl_resource($manager) {
	// 系统管理
	$acl = $manager->getResource ( 'system/preference' );
	$acl->addOperate ( 'cms', 'CMS设置' );
	
	$acl = $manager->getResource ( 'cms', '网站管理' );
	$acl->addOperate ( 'm', '网站管理', '', true );
	$acl->addOperate ( 'tpl', '模板调用' );
	$acl->addOperate ( 'approve', '审核页面' );
	$acl->addOperate ( 'submit', '提交审核' );
	// 栏目
	$acl = $manager->getResource ( 'cms/channel', '栏目管理' );
	$acl->addOperate ( 'r', '栏目管理', '', true );
	$acl->addOperate ( 'c', '新增栏目' );
	$acl->addOperate ( 'u', '编辑栏目' );
	$acl->addOperate ( 'cu', '更新页面URL' );
	// 页面
	$acl = $manager->getResource ( 'cms/page', '文章管理' );
	$acl->addOperate ( 'r', '文章管理', '', true );
	$acl->addOperate ( 'c', '新增文章' );
	$acl->addOperate ( 'u', '编辑文章' );
	$acl->addOperate ( 'd', '删除文章' );
	$acl->addOperate ( 'all', '查看所有文章' );
	// 专题
	$acl = $manager->getResource ( 'cms/topic', '专题管理' );
	$acl->addOperate ( 'r', '专题管理', '', true );
	$acl->addOperate ( 'c', '新增专题' );
	$acl->addOperate ( 'u', '编辑专题' );
	$acl->addOperate ( 'd', '删除专题' );
	$acl->addOperate ( 'all', '查看所有专题' );
	
	// 自定义页面
	$acl = $manager->getResource ( 'cms/cpage', '自定义页面管理' );
	$acl->addOperate ( 'r', '自定义页面', '', true );
	$acl->addOperate ( 'c', '新增自定义页面' );
	$acl->addOperate ( 'u', '编辑自定义页面' );
	$acl->addOperate ( 'd', '删除自定义页面' );
	
	// 导航
	$acl = $manager->getResource ( 'cms/navi', '导航管理' );
	$acl->addOperate ( 'r', '导航', '', true );
	$acl->addOperate ( 'c', '新增导航' );
	$acl->addOperate ( 'u', '编辑导航' );
	$acl->addOperate ( 'd', '删除导航' );
	
	// 区块
	$acl = $manager->getResource ( 'cms/block', '区块管理' );
	$acl->addOperate ( 'r', '区块', '', true );
	$acl->addOperate ( 'c', '新增区块' );
	$acl->addOperate ( 'u', '编辑区块' );
	$acl->addOperate ( 'd', '删除区块' );
	
	// 内链
	$acl = $manager->getResource ( 'cms/tag', '内链管理' );
	$acl->addOperate ( 'r', '内链', '', true );
	$acl->addOperate ( 'c', '新增内链' );
	$acl->addOperate ( 'u', '编辑内链' );
	$acl->addOperate ( 'dict', '更新字典' );
	$acl->addOperate ( 'd', '删除内链' );
	
	// 碎片
	$acl = $manager->getResource ( 'cms/chunk', '碎片管理' );
	$acl->addOperate ( 'r', '碎片', '', true );
	$acl->addOperate ( 'c', '新增碎片' );
	$acl->addOperate ( 'u', '编辑碎片' );
	$acl->addOperate ( 'd', '删除碎片' );
	
	// 模型
	$acl = $manager->getResource ( 'cms/model', '模型管理' );
	$acl->addOperate ( 'r', '模型', '', true );
	$acl->addOperate ( 'c', '新增模型' );
	$acl->addOperate ( 'u', '编辑模型' );
	$acl->addOperate ( 'd', '删除模型' );
	
	// 分类
	$acl = $manager->getResource ( 'cms/catalog', '分类管理' );
	$acl->addOperate ( 'r', '分类', '', true );
	$acl->addOperate ( 'c', '新增分类' );
	$acl->addOperate ( 'u', '编辑分类' );
	$acl->addOperate ( 'd', '删除分类' );
	return $manager;
}
