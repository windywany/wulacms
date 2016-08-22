<?php
/**
 * 调用远程数据条件.
 *
 * @return multitype:
 */
function get_condition_for_remote() {
	$fields [] = array ('name' => 'loop','widget' => 'hidden','default' => 'false' );
	$fields [] = array ('name' => 'datasource','label' => '数据源','note' => '数据源参数另行填写' );
	$fields [] = array ('name' => 'host','label' => '远程地址' );
	$fields [] = array ('name' => 'apiVer','label' => '远程服务器版本','note' => '默认是1','default' => '1' );
	$fields [] = array ('name' => 'group','label' => '调用组' );
	return $fields;
}
