<?php

namespace bbs\model;

use db\model\Model;

class BbsForumsModel extends Model {
	/**
	 * 获取数据型数据.
	 *
	 * @param integer $upid
	 *        	上级ID.
	 * @param integer $limit
	 *        	获取条数.
	 * @param integer $page
	 *        	页数.
	 * @return array
	 */
	public function getTreeData($upid = 0, $limit = 10, $page = 0) {
		$upid = intval ( $upid );
		$limit = intval ( $limit );
		$start = intval ( $start ) * $limit;
		$sql = dbselect ( '*' )->from ( $this->table )->setDialect ( $this->dialect )->where ( [ 'upid' => $upid,'deleted' => 0 ] )->asc ( 'sort' );
		$rst = $sql->limit ( $start, $limit )->toArray ();
		$this->checkSQL ( $sql );
		return $rst;
	}
}