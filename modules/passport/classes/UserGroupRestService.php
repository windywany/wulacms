<?php
class UserGroupRestService {
	public function rest_get_groupsTree($params, $key, $secret) {
		if (isset ( $params ['all_text'] )) {
			$groups = array ('' => $params ['all_text'] );
		} else {
			$groups = array ('' => '请选择用户组' );
		}
		dbselect ()->from ( '{user_group}' )->treeOption ( $groups, 'group_id', 'upid', 'group_name' );
		return array ('groups' => $groups );
	}
}