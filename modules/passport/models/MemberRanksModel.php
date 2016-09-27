<?php

namespace passport\models;

use db\model\Model;

class MemberRanksModel extends Model {
	public function get_level($mid = 0, $field = 'level') {
		$db = dbselect ( 'm.*' )->from ( '{member_ranks} AS mr' )->join ( '{member_rank} as m', 'mr.rank_id=m.id' )->where ( [ 'mr.mid' => $mid ] )->get ();
		if ($field) {
			if($db [$field]){
				return $db [$field];
			}else{
				return dbselect ( $field )->from ( '{member_rank}' )->asc('level')->get ($field);
			}

		}
		if(!$db){
			return dbselect ( '*' )->from ( '{member_rank}' )->asc('level')->get (0);
		}
		return $db;
	}
}