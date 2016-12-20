<?php

namespace system\controllers;

use system\model\UserTableModel;

class ColumnController extends \Controller {
	protected $checkUser = true;

	public function index($table) {
		$uid             = $this->user->getUid();
		$columns         = UserTableModel::getColumns($table, $uid);
		$data['columns'] = $columns;
		$data['table']   = $table;

		return view('table/column.tpl', $data);
	}

	public function save($table, $cols, $ord) {
		$columns       = UserTableModel::getColumns($table, $uid);
		$uid           = $this->user->getUid();
		$data['uid']   = $uid;
		$data['table'] = $table;
		$colss         = [];
		foreach ($columns as $cid => $v) {
			$colss[ $cid ]['order'] = $ord[ $cid ];
			if (!isset($cols[ $cid ])) {
				$colss[ $cid ]['show'] = 0;
			} else {
				$colss[ $cid ]['show'] = 1;
			}
		}
		if (dbselect()->from('{user_table}')->where($data)->exist('uid')) {
			dbupdate('{user_table}')->set(['columns' => json_encode($colss)])->where($data)->exec();
		} else {
			$data['columns'] = json_encode($colss);
			dbinsert($data)->into('{user_table}')->exec();
		}

		return \NuiAjaxView::refresh('');
	}
}