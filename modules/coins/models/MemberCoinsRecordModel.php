<?php
namespace coins\models;

use db\model\Model;

/**
 * User: FLY
 * Date: 2016/9/6
 * Time: 9:53
 */
class MemberCoinsRecordModel extends Model {
	private $ex_tb_member = 'member';

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
		if ($_ct) {
			$data ['total'] = $query->count('id');
		}
		$row = [];
		foreach ($query as $item) {
			$item['name'] = $this->get_name_by_mid($item['mid']);
			$row[]        = $item;
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

	/**
	 * 获取处理过的类型数组
	 * @return  array
	 */
	public function get_type_arr() {
		$types = $this->get_all();
		$ptype = [];
		foreach ($types as $row) {
			$ptype[ $row['id'] ] = $row['name'];
		}

		return $ptype;
	}

	private function get_name_by_mid($mid = 0) {
		$name = dbselect('nickname')->from($this->ex_tb_member)->where(['mid' => $mid])->get(0, 'nickname');

		return $name;
	}
}