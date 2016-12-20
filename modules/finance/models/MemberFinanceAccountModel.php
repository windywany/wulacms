<?php
namespace finance\models;

use db\model\Model;

class MemberFinanceAccountModel extends Model {
	/*分类数*/
	public function get_page_data($cond = []) {
		$data = [];
		$_sf  = $cond['_sf'];
		$_od  = $cond['_od'];
		$_cp  = $cond['_cp'];
		$_lt  = $cond['_lt'];
		$_ct  = $cond['_ct'];
		unset($cond['_sf'], $cond['_od'], $cond['_cp'], $cond['_lt'], $cond['_ct']);

		$where = $cond;
		$query = dbselect('*')->setDialect($this->dialect)->from($this->table)->where($where);
		$query->sort($_sf, $_od);
		$query->limit(($_cp - 1) * $_lt, $_lt);

		$data ['total'] = $query->count('id');
		$row            = [];
		foreach ($query as $item) {
			$row[] = $item;
		}
		$data ['rows'] = $row;

		return $data;
	}

	/**
	 * 据查询条件获取单条记录
	 *
	 * @param  array $cond
	 *
	 * @return  boolean $res
	 */
	public function get_one($cond = []) {
		$res = dbselect('*')->from($this->table)->where($cond)->get(0);

		return $res;
	}

	/**
	 * @param array $cond
	 *
	 * @return mixed
	 */
	public function get_all($cond = []) {
		$cond['id >'] = 0;
		$res          = dbselect('*')->from($this->table)->where($cond)->toArray();

		return $res;
	}
}