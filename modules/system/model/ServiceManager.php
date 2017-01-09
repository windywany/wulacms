<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace system\model;

use db\model\Model;

class ServiceManager extends Model {
	protected function config() {
		$this->table = 'user_group_service';
	}

	/**
	 * 获取用户组可用的服务.
	 *
	 * @param int  $gid
	 * @param bool $enabled
	 * @param null $service
	 *
	 * @return array|null
	 */
	public function getGroupService($gid, $enabled = true, $service = null) {
		$where['group_id'] = $gid;
		if ($service) {
			$where['service'] = $service;
		}
		if ($enabled) {
			$where['enabled'] = 1;
		}
		$query = $this->select('*')->where($where);

		return $this->prepareService($service, $query);
	}

	/**
	 * 获取用户服务.
	 *
	 * @param int         $uid
	 * @param bool        $enabled
	 * @param null|string $service
	 *
	 * @return array
	 */
	public function getUserService($uid, $enabled = true, $service = null) {
		$where['mid'] = $uid;

		if ($service) {
			$where['service'] = $service;
		}
		if ($enabled) {
			$where['valid'] = 1;
		}
		$query = dbselect('*')->from('{user_service}')->where($where);

		return $this->prepareService($service, $query);
	}

	/**
	 * @param string|null   $service
	 * @param \QueryBuilder $query
	 *
	 * @return array
	 */
	private function prepareService($service, $query) {
		if ($service) {
			$serv = $query->get(0);
			if ($serv) {
				$serv['config'] = @json_decode($serv['config'], true);
			}
		} else {
			$serv = [];
			foreach ($query as $q) {
				$id          = $q['service'];
				$q['config'] = @json_decode($q['config'], true);
				$serv[ $id ] = $q;
			}
		}

		return $serv;
	}
}