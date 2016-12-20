<?php

namespace coins\models;

use db\model\Model;

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/9/6
 * Time: 9:53
 */
class MemberCoinsAccountModel extends Model {
	/* 分类数 */
	public function get_page_data($cond = []) {
		$data = [];
		$_sf  = $cond ['_sf'];
		$_od  = $cond ['_od'];
		$_cp  = $cond ['_cp'];
		$_lt  = $cond ['_lt'];
		$_ct  = $cond ['_ct'];
		unset ($cond ['_sf'], $cond ['_od'], $cond ['_cp'], $cond ['_lt'], $cond ['_ct']);

		$where = $cond;
		$query = dbselect('*')->setDialect($this->dialect)->from($this->table)->where($where);
		$query->sort($_sf, $_od);
		$query->limit(($_cp - 1) * $_lt, $_lt);
		if ($_ct) {
			$data ['total'] = $query->count('id');
		}
		$row = [];
		foreach ($query as $item) {
			$row [] = $item;
		}
		$data ['rows'] = $row;

		return $data;
	}

	/**
	 * 据查询条件获取单条记录
	 *
	 * @param array $cond
	 *
	 * @return boolean $res
	 */
	public function get_one($cond = []) {
		$res = dbselect('*')->from($this->table)->where($cond)->get(0);

		return $res;
	}

	public function get_field($cond = [], $field) {
		if (!is_array($cond)) {
			$cond = ['id' => $cond];
		}
		$res = dbselect($field)->from($this->table)->where($cond)->get($field);

		return $res;
	}

	/**
	 *
	 * @param array $cond
	 *
	 * @return mixed
	 */
	public function get_all($cond = []) {
		$cond ['id >'] = 0;
		$res           = dbselect('*')->from($this->table)->where($cond)->toArray();

		return $res;
	}

	public function init($mid, $type) {
		$id = $this->get(['mid' => $mid, 'type' => $type], 'id');
		if (!$id ['id']) {
			$uname     = dbselect('nickname')->from('{member}')->where(['mid' => $mid])->get('nickname');
			$typeModel = new \coins\models\MemberCoinsTypeModel ();
			$type_info = $typeModel->get_one(['type' => $type]);
			$set       = ['create_time' => time(), 'mid' => $mid, 'type' => $type, 'mname' => $uname, 'can_withdraw' => $type_info ['can_withdraw'], 'use_priority' => $type_info ['use_priority']];
			$res       = $this->create($set);

			return $res;
		} else {
			return $id ['id'];
		}
	}
}