<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 角色管理.
 *
 * @author Guangfeng
 */
class RoleController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('data' => 'r:account/role','index' => 'r:account/role','add' => 'c:account/role','edit' => 'u:account/role','save' => 'role_id|u:account/role;c:account/role','del' => 'd:account/role','acl' => 'acl:account/role','acl_post' => 'acl:account/role','acldata' => 'acl:account/role' );
	
	/**
	 * 角色首页.
	 *
	 * @return SmartyView
	 */
	public function index($type = 'admin') {
		$data = array ('canDelRole' => icando ( 'd:account/role' ) );
		$data ['canAddRole'] = icando ( 'c:account/role' );
		$data ['types'] = UserGroupForm::getGroupTypes ();
		$data ['type'] = $type;
		return view ( 'role/index.tpl', $data );
	}
	/**
	 * 新增角色.
	 *
	 * @return SmartyView
	 */
	public function add($type) {
		$form = new UserRoleForm ( array ('role_id' => 0 ) );
		$role ['rules'] = $form->rules ();
		$role ['types'] = UserGroupForm::getGroupTypes ();
		$role ['type'] = $type;
		return view ( 'role/form.tpl', $role );
	}
	/**
	 * 编辑.
	 *
	 * @param int $id
	 */
	public function edit($id) {
		$id = intval ( $id );
		$role = dbselect ( '*' )->from ( 'user_role' )->where ( array ('role_id' => $id ) )->get ( 0 );
		if (empty ( $role )) {
			return NuiAjaxView::error ( '角色不存在.' );
		} else {
			$form = new UserRoleForm ( $role );
			$role ['rules'] = $form->rules ();
			$role ['types'] = UserGroupForm::getGroupTypes ();
			return view ( 'role/form.tpl', $role );
		}
	}
	/**
	 * 删除.
	 *
	 * @param string $rid
	 */
	public function del($rid = '') {
		$rid = safe_ids ( $rid, ',', true );
		$con = array ('role_id IN' => $rid );
		$rst = dbdelete ()->from ( '{user_role}' )->where ( $con )->exec ();
		if ($rst) {
			// 删除用户的角色
			dbdelete ()->from ( '{user_has_role}' )->where ( $con )->exec ();
			// 删除角色的权限
			dbdelete ()->from ( '{user_role_acl}' )->where ( $con )->exec ();
			return NuiAjaxView::ok ( '角色已经删除.', 'click', '#refresh' );
		} else {
			return NuiAjaxView::error ( '删除角色出错啦:' . DatabaseDialect::$lastErrorMassge );
		}
	}
	/**
	 * 保存角色信息.
	 */
	public function save() {
		$form = new UserRoleForm ();
		$role = $form->valid ();
		if ($role) {
			if (empty ( $role ['role_id'] )) {
				unset ( $role ['role_id'] );
				$rst = dbinsert ( $role )->into ( '{user_role}' )->exec ();
			} else {
				$role_id = $role ['role_id'];
				unset ( $role ['role_id'] );
				$rst = dbupdate ( '{user_role}' )->set ( $role )->where ( array ('role_id' => $role_id ) )->exec ();
			}
			if ($rst) {
				return NuiAjaxView::click ( '#btn-rtn-role', '保存角色完成' );
			} else {
				return NuiAjaxView::error ( '保存角色出错啦:数据库操作异常.' );
			}
		} else {
			return NuiAjaxView::validate ( 'UserRoleForm', '保存角色出错啦,数据校验失败!', $form->getErrors () );
		}
	}
	/**
	 * 排序.
	 *
	 * @param int $id
	 * @param int $sort
	 * @return NuiAjaxView
	 */
	public function csort($id, $sort) {
		$id = intval ( $id );
		$sort = intval ( $sort );
		if (! empty ( $id )) {
			dbupdate ( '{user_role}' )->set ( array ('priority' => $sort ) )->where ( array ('role_id' => $id ) )->exec ();
		}
		return NuiAjaxView::ok ( '权重修改完成.' );
	}
	/**
	 * 角色数据.
	 *
	 * @param int $_cp
	 * @param int $_lt
	 * @param string $_sf
	 * @param string $_od
	 * @param int $_ct
	 * @return SmartyView
	 */
	public function data($_cp = 1, $_lt = 20, $_sf = 'role_id', $_od = 'd', $_ct = 0) {
		$rows = dbselect ( '*' )->from ( '{user_role}' )->limit ( ($_cp - 1) * $_lt, $_lt );
		$rows->sort ( $_sf, $_od );
		$role_name = rqst ( 'role_name', '' );
		$role = rqst ( 'role' );
		$type = rqst ( 'type', 'admin' );
		$where = array ();
		if (! empty ( $role_name )) {
			$where ['role_name LIKE'] = '%' . $role_name . '%';
		}
		if (! empty ( $role )) {
			$where ['role LIKE'] = '%' . $role . '%';
		}
		if (! empty ( $type )) {
			$where ['type'] = $type;
		}
		$rows->where ( $where );
		$total = '';
		if ($_ct) {
			$total = $rows->count ( 'role_id' );
		}
		$data = array ('total' => $total,'rows' => $rows,'hasAcl' => icando ( 'acl:account/role' ) );
		$data ['canEditRole'] = icando ( 'u:account/role' );
		return view ( 'role/data.tpl', $data );
	}
	/**
	 * 权限设置页面.
	 *
	 * @param int $rid
	 * @return SmartyView
	 */
	public function acl($rid) {
		$role = dbselect ( 'role_name,type' )->from ( '{user_role}' )->where ( array ('role_id' => intval ( $rid ) ) )->get ( 0 );
		if ($role) {
			$data ['role_id'] = $rid;
			$data ['role_name'] = $role ['role_name'];
			$data ['role_type'] = $role ['type'];
			return view ( 'role/acl.tpl', $data );
		} else {
			Response::respond ( 500 );
		}
	}
	/**
	 * 保存角色权限.
	 *
	 * @param array $acl
	 * @param int $role_id
	 * @return NuiAjaxView
	 */
	public function acl_post($acl, $role_id, $od) {
		$role_id = intval ( $role_id );
		if ($role_id) {
			$rst = true;
			$beDeleteingIds = array ();
			$priority = time ();
			if (is_array ( $acl ) && ! empty ( $acl )) {
				$acls = array ();
				foreach ( $acl as $key => $v ) {
					if (! is_numeric ( $v )) {
						$beDeleteingIds [] = $key;
						continue;
					}
					$v = $v === '1' ? 1 : 0;
					$data = array ('role_id' => $role_id,'resource' => $key );
					$allowed = dbselect ( 'allowed' )->from ( '{user_role_acl}' )->where ( $data )->get ( 'allowed' );
					if (is_numeric ( $allowed )) {
						if ($allowed != $v) {
							dbupdate ( '{user_role_acl}' )->set ( array ('allowed' => $v ) )->where ( $data )->exec ();
						}
					} else {
						$data ['allowed'] = $v;
						$data ['priority'] = 0;
						$acls [] = $data;
					}
				}
				if (! empty ( $beDeleteingIds )) {
					dbdelete ()->from ( '{user_role_acl}' )->where ( array ('role_id' => $role_id,'resource IN' => $beDeleteingIds ) )->exec ();
				}
				if ($acls) {
					$rst = dbinsert ( $acls, true )->into ( '{user_role_acl}' )->exec ();
				}
			}
			if ($rst) {
				return NuiAjaxView::ok ( '成功保存角色权限.' );
			} else {
				return NuiAjaxView::error ( '保存角色权限出错,数据库操作异常.' );
			}
		} else {
			return NuiAjaxView::error ( '保存权限出错,非法的角色编号.' );
		}
	}
	public function acldata($rid, $_tid = '') {
		$aclResources = apply_filter ( 'get_acl_resource', new AclResourceManager () );
		$node = $aclResources->getResource ( $_tid );
		$nodes = $node->getNodes ();
		$data ['ops'] = $node->getOperations ();
		$data ['nodes'] = $nodes;
		$data ['acl'] = dbselect ( 'resource,allowed' )->from ( 'user_role_acl' )->where ( array ('role_id' => $rid ) )->toArray ( null, 'resource' );
		$data ['parent'] = $_tid;
		$data ['options'] = array ('' => '继承','1' => '允许','0' => '禁止' );
		$data ['debuging'] = bcfg ( 'develop_mode' );
		return view ( 'role/acldata.tpl', $data );
	}
}