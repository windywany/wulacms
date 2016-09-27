<?php

namespace passport\models;

use db\model\Model;

class MemberRankModel extends Model {
	/**
	 *
	 * @param array $cond        	
	 * @return mixed
	 */
	public function get_all($cond = []) {
		$res = dbselect ( '*' )->from ( $this->table )->where ( $cond )->desc ( 'level' )->toArray ();
		return $res;
	}
	public function get_level($coins) {
		$res = dbselect ( 'id' )->from ( $this->table )->where ( [ 'coins >=' => $coins ] )->asc ( 'coins' )->get ( 'id' );
		if (! $res) {
			$res = dbselect ( 'id' )->from ( $this->table )->desc ( 'coins' )->get ( 'id' );
		}
		return $res;
	}
}