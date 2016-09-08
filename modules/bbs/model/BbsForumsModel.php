<?php

namespace bbs\model;

use db\model\Model;

class BbsForumsModel extends Model {
	public function get($id, $fields = '*') {
		$data = parent::get($id, $fields);
		if (isset($data['masters'])) {
			$masters = @json_decode($data['masters'],true);
			if ($masters) {
				$i = 2;
				foreach ($masters as $m) {
					if ($m['master']) {
						$data['master1'] = $m['mid'];
					} else {
						$data[ 'master' . $i ] = $m['mid'];
						$i++;
					}
				}
			}
		}

		return $data;
	}

	/**
	 * 获取数据型数据.
	 *
	 * @param integer $upid
	 *            上级ID.
	 * @param integer $limit
	 *            获取条数.
	 * @param integer $page
	 *            页数.
	 *
	 * @return array
	 */
	public function getTreeData($upid = 0, $limit = 10, $page = 0) {
		$upid  = intval($upid);
		$limit = intval($limit);
		$start = intval($page) * $limit;
		$sql   = dbselect('*')->from($this->table)->setDialect($this->dialect)->where(['upid' => $upid, 'deleted' => 0])->asc('sort');
		$rst   = $sql->limit($start, $limit)->toArray();
		$this->checkSQL($sql);

		return $rst;
	}

	public function update($data, $con = null, $cb = null) {
		$this->setMasters($data);

		return parent::update($data, $con, $cb);
	}

	public function create($data, $cb = null) {
		$this->setMasters($data);

		return parent::create($data, $cb);
	}

	public static function on_destroy_bbs_forums($ids) {
		$model = new BbsForumsModel();
		$model->delete(['id IN' => $ids]);
	}

	private function setMasters(&$data) {
		$masters = [];
		if (isset($data['master1'])) {
			$masters[] = ['mid' => $data['master1'], 'master' => 1, 'name' => dbselect()->from('{member}')->where(['mid' => $data['master1']])->get('nickname')];
			unset($data['master1']);
		}

		if (isset($data['master2'])) {
			$masters[] = ['mid' => $data['master2'], 'master' => 0, 'name' => dbselect()->from('{member}')->where(['mid' => $data['master2']])->get('nickname')];
			unset($data['master2']);
		}

		if (isset($data['master3'])) {
			$masters[] = ['mid' => $data['master3'], 'master' => 0, 'name' => dbselect()->from('{member}')->where(['mid' => $data['master2']])->get('nickname')];
			unset($data['master3']);
		}

		$data['masters'] = json_encode($masters);
	}
}