<?php
/**
 * 调取系统分类的数据源.
 *
 * @param array $con
 * @return CtsData
 */
function system_catalog_provider($con, $tplvars) {
	$type = get_condition_value ( 'type', $con );
	$upid = get_condition_value ( 'upid', $con );
	$id = get_condition_value ( 'id', $con );
	if (! empty ( $id )) {
		if (is_array ( $id )) {
			$id = implode ( ',', $id );
		}
		$ids = safe_ids2 ( $id );
		$query = dbselect ( '*' )->from ( '{catalog}' )->where ( array ('id IN' => $ids ) );
	}
	$where = array ();
	if ($type) {
		$where ['type'] = $type;
		if (is_numeric ( $upid ) && ! empty ( $upid )) {
			$where ['upid'] = $upid;
		}
	}
	if ($where) {
		$where ['deleted'] = 0;
		$query = dbselect ( '*' )->from ( '{catalog}' )->where ( $where );
	}
	if (isset ( $query )) {
		return new CtsData ( $query->toArray () );
	} else {
		return new CtsData ();
	}
}
/**
 * 调取系统分类的数据源条件.
 *
 * @return array
 */
function get_condition_for_catalog() {
	$fields = array ();
	$fields [] = array ('name' => 'type','label' => '分类类型','note' => '哪种类型的分类' );
	$fields [] = array ('name' => 'upid','label' => '上级分类','note' => '上级分类编号.' );
	$fields [] = array ('name' => 'id','label' => '编号','note' => '加载指定的分类项' );
	return $fields;
}
function get_fieldmap_for_catalog() {
	return array ('id' => 'id','name' => 'name' );
}
