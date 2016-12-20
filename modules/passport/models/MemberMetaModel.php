<?php
namespace passport\models;

use db\model\Model;

class MemberMetaModel extends Model {

	protected function config() {
		$this->autoIncrement = false;
		$this->primaryKeys   = ['mid'];
	}

	/**
	 * 保存用户角色.
	 *
	 * @param int   $mid
	 * @param array $roles
	 */
	public function saveRoles($mid, $roles) {
		if ($mid) {
			dbdelete()->from('{member_has_role}')->setDialect($this->dialect)->where(array('mid' => $mid))->exec();
			$roleName = '';
			$time     = time();
			if (!empty ($roles)) {
				$datas = array();
				foreach ($roles as $role_id) {
					$datas [] = array('mid' => $mid, 'role_id' => $role_id, 'sort' => 0);
				}
				dbinsert($datas, true)->into('{member_has_role}')->setDialect($this->dialect)->exec();
				$roleNames = dbselect('role_name')->from('{user_role}')->where(array('role_id IN' => $roles))->toArray('role_name');
				$roleName  = implode(',', $roleNames);
			}
			$this->save(array('mid' => $mid, 'name' => 'roles', 'value' => $roleName, 'create_time' => $time, 'update_time' => $time, 'update_uid' => $mid), array('mid' => $mid, 'name' => 'roles'));
		}
	}
}