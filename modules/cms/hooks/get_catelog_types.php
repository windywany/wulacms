<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 注册分类类型.
 *
 * @param array $types        	
 * @return array
 */
function filter_for_get_catelog_types($types) {
	$types ['chunk'] = array ('name' => '碎片分类' );
	$types ['block'] = array ('name' => '区块分类' );
	$types ['navi'] = array ('name' => '导航菜单' );
	return $types;
}