<?php
/**
 * 通行证 RESTful Service Implimentetion.
 * @author ngf
 *
 */
class PassportRestService {
	/**
	 * 登录成功后应该立即调用此方法以获取用户信息.
	 *
	 * @param unknown $params        	
	 * @param unknown $key        	
	 * @param unknown $secret        	
	 * @return multitype:Ambigous <Ambigous, NULL, unknown, multitype:> |multitype:string
	 */
	public function rest_get_userinfo($params, $key, $secret) {
		$token = isset ( $params ['token'] ) ? $params ['token'] : false;
		if ($token) {
			$user_id = dbselect ( 'user_id' )->from ( '{passport_session}' )->where ( array ('session_id' => $token,'expire_time >' => time () ) )->get ( 'user_id' );
			if ($user_id) {
				$user = $this->rest_get_getUser ( array ('user_id' => $user_id ), $key, $secret );
				if ($user ['user']) {
					unset ( $user ['user'] ['group_id'] );
					dbdelete ()->from ( '{passport_session}' )->where ( array ('session_id' => $token ) )->exec ();
					dbdelete ()->from ( '{passport_session}' )->where ( array ('expire_time <' => time () ) )->exec ();
					return $user;
				} else {
					return array ('error' => '503','message' => 'User not found' );
				}
			} else {
				return array ('error' => '502','message' => 'session expire' );
			}
		} else {
			return array ('error' => '501','message' => 'bad token' );
		}
	}
	/**
	 * 取用户信息.
	 *
	 * @param unknown $params        	
	 * @param unknown $key        	
	 * @param unknown $secret        	
	 * @return multitype:Ambigous <Ambigous, NULL, unknown, multitype:> |multitype:string
	 */
	public function rest_get_getUser($params, $key, $secret) {
		$user_id = $params ['user_id'];
		if ($user_id) {
			$user = dbselect ( '*' )->from ( '{user}' )->where ( array ('user_id' => $user_id,'status' => 1 ) )->get ( 0 );
			if ($user) {
				unset ( $user ['passwd'] );
				unset ( $user ['deleted'] );
				return array ('user' => $user );
			} else {
				return array ('error' => '503','message' => 'User not found' );
			}
		} else {
			return array ('error' => '501','message' => 'no user id specified.' );
		}
	}
	/**
	 * ACL 权限.
	 *
	 * @param unknown $params        	
	 * @param unknown $key        	
	 * @param unknown $secret        	
	 * @return multitype:unknown |multitype:string
	 */
	public function rest_get_getAcls($params, $key, $secret) {
		$operations = array ();
		$user_id = $params ['user_id'];
		if ($user_id) {
			$acls = dbselect ( 'resource,allowed' )->from ( '{user_role_acl} AS URC' );
			$acls->join ( '{user_has_role} AS UR', 'UR.role_id = URC.role_id' );
			$acls->where ( array ('UR.user_id' => $user_id ) );
			$acls->sort ( 'priority', 'a' );
			foreach ( $acls as $acl ) {
				$res = $acl ['resource'];
				$operations [$res] = $acl ['allowed'];
			}
		}
		if ($operations) {
			return $operations;
		} else {
			return array ('error' => '500','message' => 'no acl for the user.' );
		}
	}
	/**
	 * 用户角色.
	 *
	 * @param unknown $params        	
	 * @param unknown $key        	
	 * @param unknown $secret        	
	 */
	public function rest_get_getRoles($params, $key, $secret) {
		$roles = array ();
		$user_id = $params ['user_id'];
		if ($user_id) {
			$roles = dbselect ( 'UR.*' )->from ( '{user_has_role} AS UHR' );
			$roles->join ( '{user_role} AS UR', 'UHR.role_id = UR.role_id' );
			$roles->where ( array ('UHR.user_id' => $user_id ) );
			$roles = $roles->toArray ( null, 'role' );
		}
		if ($roles) {
			return array ('roles' => $roles );
		} else {
			return array ('error' => '500','message' => 'no acl for the user.' );
		}
	}		
}
