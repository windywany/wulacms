<?php

namespace system\model;

use db\model\Model;

class UserTableModel extends Model {

	public static function echoSetButton($id) {
		$url = tourl('system/column');

		return '<a href="' . $url . urlencode($id) . '" target="dialog" dialog-title="表格列设置" dialog-width="400" dialog-model="true"><i class="fa fa-th-list"></i></a>';
	}

	public static function echoHead($id) {
		$user    = whoami();
		$uid     = $user->getUid();
		$columns = self::getColumns($id, $uid);
		$heads   = [];

		foreach ($columns as $cid => $col) {
			if ($col['show']) {
				$heads[] = '<th width="' . $col['width'] . '"' . ($col['sort'] ? 'data-sort="' . $col['sort'] . '"' : '') . '>' . $col['name'] . '</th>';
			}
		}

		return implode('', $heads);
	}

	public static function echoRow($id, $data, $extras = []) {
		static $columns = [];
		if (!isset($columns[ $id ])) {
			$user           = whoami();
			$uid            = $user->getUid();
			$column         = self::getColumns($id, $uid);
			$columns[ $id ] = $column;
		}
		$rows = [];
		foreach ($columns[ $id ] as $cid => $col) {
			if ($col['show']) {
				$rows[] = '<td>' . (is_callable($col['render']) ? call_user_func_array($col['render'], [$data[ $cid ], $data, $extras]) : $data[ $cid ]) . '</td>';
			}
		}

		return implode('', $rows);
	}

	public static function colspan($id, $num) {
		static $columns = [];
		if (!isset($columns[ $id ])) {
			$user   = whoami();
			$uid    = $user->getUid();
			$column = self::getColumns($id, $uid);

			foreach ($column as $cid => $col) {
				if ($col['show']) {
					$num += 1;
				}
			}

			$columns[ $id ] = $num;
		}

		return $columns[ $id ];
	}

	/**
	 * @param string $id
	 * @param int    $uid
	 *
	 * @return array
	 */
	public static function getColumns($id, $uid) {
		static $gcolumns = [];
		if (isset($gcolumns[ $id ][ $uid ])) {
			return $gcolumns[ $id ][ $uid ];
		}
		$cols    = dbselect()->from('{user_table}')->where(['uid' => $uid, 'table' => $id])->get('columns');
		$columns = apply_filter('get_columns_of_' . $id, []);

		if ($cols) {
			$cols = @json_decode($cols, true);
			foreach ($columns as $col => $cfg) {
				if (isset($cols[ $col ]) && $cols[ $col ]['show']) {
					$columns[ $col ]['show'] = true;
				} else {
					$columns[ $col ]['show'] = false;
				}
				if (isset($cols[ $col ])) {
					$columns[ $col ]['order'] = $cols[ $col ]['order'];
				}
			}
		}
		uasort($columns, \ArrayComparer::compare('order'));
		$gcolumns[ $id ][ $uid ] = $columns;

		return $columns;
	}
}