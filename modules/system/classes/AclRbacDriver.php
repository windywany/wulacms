<?php
/**
 * 权限验证类.
 * @author Guangfeng
 *
 */
class AclRbacDriver implements IRbac {
	/**
	 * (non-PHPdoc)
	 *
	 * @see IRbac::icando()
	 */
	public function icando($resource, $passport) {
		if ($passport->isLogin ()) {
			$uid = $passport->getUid ();
			if ($uid == 1) {
				// 站点超级管理员.
				return true;
			}
			$acls = $passport->getAttr ( 'user_role_acl', false );
			if ($acls === false) {
				$acls = $this->loadAcl ( $uid );
				$passport->setAttr ( 'user_role_acl', $acls );
				$passport->save ();
			}
			if (empty ( $acls )) {
				return false;
			}
			if (isset ( $acls [$resource] )) {
				return $acls [$resource] == 1;
			} else {
				$resources = explode ( ':', $resource );
				if (count ( $resource ) < 1) {
					// resource应该是操作与资源的组合以:相连
					return false;
				}
				$resources [0] = '*';
				$id = $resources [1];
				$ids = explode ( '/', $resources [1] );
				while ( count ( $ids ) > 0 ) {
					$resources [1] = implode ( '/', $ids );
					$resource = implode ( ':', $resources );
					if (isset ( $acls [$resource] )) {
						return $acls [$resource] == 1;
					}
					array_pop ( $ids );
				}
			}
		}
		return false;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see IRbac::iam()
	 * @param array|string $roles        	
	 * @param Passport $passport        	
	 */
	public function iam($roles, $passport) {
		$urs = $passport->getAttr ( 'user_roles', false );
		if ($urs === false) {
			$urs = $this->loadRoles ( $passport->getUid () );
			$passport->setAttr ( 'user_roles', $urs );
			$passport->save ();
		}
		if (! empty ( $urs )) {
			if (! is_array ( $roles )) {
				$roles = array ($roles );
			}
			foreach ( $roles as $role ) {
				if (! in_array ( $role, $urs )) {
					return false;
				}
			}
			return true;
		}
		return false;
	}
	/**
	 * 加载用户的权限.
	 *
	 * @param int $user_id        	
	 * @return array
	 */
	private function loadAcl($user_id) {
		$operations = array ();
		if ($user_id) {
			$acls = dbselect ( 'resource,allowed' )->from ( '{user_role_acl} AS URC' );
			$acls->join ( '{user_has_role} AS UR', 'UR.role_id = URC.role_id' );
			$acls->join ( '{user_role} AS R', 'R.role_id = URC.role_id' );
			$acls->where ( array ('UR.user_id' => $user_id ) );
			$acls->sort ( 'R.priority', 'a' );
			foreach ( $acls as $acl ) {
				$res = $acl ['resource'];
				$operations [$res] = $acl ['allowed'];
			}
		}
		return $operations;
	}
	private function loadRoles($user_id) {
		if ($user_id) {
			$roles = dbselect ( 'UR.role' )->from ( '{user_has_role} AS UHR' );
			$roles->join ( '{user_role} AS UR', 'UHR.role_id = UR.role_id' );
			$roles->where ( array ('UHR.user_id' => $user_id ) );
			return $roles->toArray ( 'role' );
		}
		return array ();
	}
}