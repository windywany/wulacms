<?php
/*
 * KissCms
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 用户账户管理器.
 *
 * @author Guangfeng Ning <windywany@gmail.com>
 */
class UserController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('data' => 'r:account/user','index' => 'r:account/user','add' => 'c:account/user','edit' => 'u:account/user','save' => 'user_id|u:account/user;c:account/user','del' => 'd:account/user' );
	/**
	 * 首页.
	 *
	 * @return SmartyView
	 */
	public function index() {
		$data = array ();
		$data ['groups'] = array ('' => '-请选择用户组-' );
		$disable_client = ! bcfg ( 'enable_client@passport' );
		$data ['canAddUser'] = $disable_client && icando ( 'c:account/user' );
		$data ['canDelUser'] = $disable_client && icando ( 'd:account/user' );
		dbselect ()->from ( '{user_group}' )->treeOption ( $data ['groups'], 'group_id', 'upid', 'group_name' );
		$data ['all_roles'] = dbselect ( 'role_id,role_name' )->from ( '{user_role}' )->where ( array ('type' => 'admin' ) )->toArray ( 'role_name', 'role_id', array ('' => '-请选择角色-' ) );
		
		return view ( 'user/index.tpl', $data );
	}
	/**
	 * 新增用户.
	 *
	 * @return SmartyView
	 */
	public function add() {
		$data = array ();
		$data ['groups'] = array ('' => '--请选择用户组--' );
		dbselect ()->from ( '{user_group}' )->treeOption ( $data ['groups'], 'group_id', 'upid', 'group_name' );
		$form = new SystemUserForm ( array ('user_id' => 0 ) );
		$data ['rules'] = $form->rules ();
		$data ['roles'] = array ();
		$data ['status'] = '1';
		$data ['all_roles'] = dbselect ( 'role_id,role_name' )->where ( array ('type' => 'admin' ) )->from ( '{user_role}' );
		return view ( 'user/form.tpl', $data );
	}
	/**
	 * 编辑用户.
	 *
	 * @param int $id
	 * @return NuiAjaxView SmartyView
	 */
	public function edit($id) {
		$id = intval ( $id );
		$user = dbselect ( '*' )->from ( '{user}' )->where ( array ('user_id' => $id ) )->get ( 0 );
		if (empty ( $user )) {
			return NuiAjaxView::error ( '用户不存在.' );
		} else {
			$user ['groups'] = array ('' => '--请选择用户组--' );
			dbselect ()->from ( '{user_group}' )->treeOption ( $user ['groups'], 'group_id', 'upid', 'group_name' );
			$form = new SystemUserForm ( $user );
			$user ['roles'] = dbselect ( 'role_id' )->from ( '{user_has_role}' )->where ( array ('user_id' => $id ) )->toArray ( 'role_id' );
			$user ['all_roles'] = dbselect ( 'role_id,role_name' )->from ( '{user_role}' )->where ( array ('type' => 'admin' ) );
			$form->removeRlue ( 'passwd', 'required' );
			$user ['rules'] = $form->rules ( true );
			return view ( 'user/form.tpl', $user );
		}
	}
	public function del($uids) {
		$uids = safe_ids ( $uids, ',', true );
		foreach ( $uids as $id => $key ) {
			if ($key === 1) {
				unset ( $uids [$id] );
			}
		}
		if (! empty ( $uids )) {
			$data ['deleted'] = 1;
			$data ['update_time'] = time ();
			$data ['update_uid'] = $this->user->getUid ();
			if (dbupdate ( '{user}' )->set ( $data )->where ( array ('user_id IN' => $uids ) )->exec ()) {
				$recycle = new DefaultRecycle ( $uids, 'User', 'user', 'UID:{user_id}; 用户名:{username}', 'user_id' );
				RecycleHelper::recycle ( $recycle );
				fire ( 'on_delete_user', $uids );
				ActivityLog::warn ( 'delete users:%s', implode ( ',', $uids ) );
				return NuiAjaxView::ok ( '已删除', 'click', '#refresh' );
			} else {
				return NuiAjaxView::error ( '数据库操作失败.' );
			}
		} else {
			Response::showErrorMsg ( '错误的编号', 404 );
		}
	}
	public function profile() {
		$id = $this->user->getUid ();
		$user = dbselect ( '*' )->from ( '{user}' )->where ( array ('user_id' => $id ) )->get ( 0 );
		if (empty ( $user )) {
			return NuiAjaxView::error ( '用户不存在.' );
		} else {
			$user ['themes'] = array ('0' => '默认','1' => 'Dark Elegance','2' => 'Ultra Light','3' => 'Google Skin','4' => 'PixelSmash','5' => 'Glass' );
			$metas = dbselect ( 'meta_name,meta_value' )->from ( '{user_meta}' )->where ( array ('user_id' => $id ) )->toArray ( 'meta_value', 'meta_name' );
			if ($metas) {
				$user = array_merge ( $user, $metas );
			}
			$form = new UserProfileForm ( $user );
			$user ['rules'] = $form->rules ();
			return view ( 'user/profile.tpl', $user );
		}
	}
	public function profile_post() {
		$form = new UserProfileForm ();
		$user_id = $this->user->getUid ();
		$account = $this->user->getAccount ();
		$user = $form->valid ();
		if ($user && $user_id) {
			unset ( $user ['passwd1'] );
			unset ( $user ['user_id'] );
			if (! empty ( $user ['passwd'] ) && $account != 'demo') {
				$user ['passwd'] = md5 ( $user ['passwd'] );
			} else {
				unset ( $user ['passwd'] );
			}
			$user ['update_time'] = time ();
			$userDao = new UserDao ();
			$rst = $userDao->updateUser ( $user, $user_id );
			if ($rst) {
				$theme = rqst ( 'theme' );
				UserProfileForm::saveUserMeta ( $user_id, 'theme', $theme );
				$mot = rqst ( 'menu_on_top', 0 );
				UserProfileForm::saveUserMeta ( $user_id, 'menu_on_top', $mot );
				$this->user->setUserName ( $user ['nickname'] );
				$this->user->setAttr ( 'theme', $theme );
				$this->user->setAttr ( 'menu_on_top', $mot );
				$this->user->save ();
				return NuiAjaxView::redirect ( '个人设置保存成功.', tourl ( 'dashboard' ) );
			} else {
				return NuiAjaxView::error ( '保存个人设置出错啦:数据库操作失败.' );
			}
		} else {
			return NuiAjaxView::validate ( 'UserProfileForm', '数据校验失败.', $form->getErrors () );
		}
	}
	/**
	 * 保存.
	 */
	public function save() {
		$form = new SystemUserForm ();
		$user_id = irqst ( 'user_id' );
		if ($user_id) {
			$form->removeRlue ( 'passwd', 'required' );
		}
		$user = $form->valid ();
		if ($user) {
			unset ( $user ['passwd1'] );
			unset ( $user ['user_id'] );
			if (empty ( $user ['group_id'] )) {
				$user ['group_id'] = 0;
			}
			if (! empty ( $user ['passwd'] )) {
				$user ['passwd'] = md5 ( $user ['passwd'] );
			} else {
				unset ( $user ['passwd'] );
			}
			if (empty ( $user ['nickname'] )) {
				$user ['nickname'] = $user ['username'];
			}
			$user ['status'] = $user ['status'] == '1' ? 1 : 0;
			$roles = $user ['roles'];
			unset ( $user ['roles'] );
			$user ['update_time'] = time ();
			$useDao = new UserDao ();
			if (empty ( $user_id )) {
				// 新增
				$user ['registered'] = time ();
				$user ['ip'] = $_SERVER ['REMOTE_ADDR'];
				$user_id = $rst = $useDao->insertUser ( $user );
			} else {
				// 修改
				$rst = $useDao->updateUser ( $user, $user_id );
			}
			if ($rst) {
				$this->_saveRoles ( $user_id, $roles );
				return NuiAjaxView::ok ( '成功保存用户', 'click', '#btn-rtn-user' );
			} else {
				return NuiAjaxView::error ( '保存用户出错啦:数据库操作失败.' );
			}
		} else {
			return NuiAjaxView::validate ( 'SystemUserForm', '保存用户出错啦,数据校验失败!', $form->getErrors () );
		}
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
	public function data($_cp = 1, $_lt = 20, $_sf = 'user_id', $_od = 'd', $_ct = 0) {
		$rows = dbselect ( 'U.*,UG.group_name,UM.meta_value AS roles' )->from ( '{user} AS U' )->limit ( ($_cp - 1) * $_lt, $_lt );
		$rows->join ( '{user_group} AS UG', 'U.group_id = UG.group_id' );
		$rows->join ( '{user_meta} AS UM', "U.user_id = UM.user_id AND UM.meta_name = 'roles'" );
		$rows->sort ( $_sf, $_od );
		$role_name = rqst ( 'role_name', '' );
		$role = rqst ( 'role' );
		$where = Condition::where ( 'U.group_id', 'status' );
		$where ['U.deleted'] = 0;
		$ktype = rqst ( 'ktype', 'username' );
		$keyword = rqst ( 'keyword' );
		if ($keyword) {
			if ($ktype == 'user_id' || $ktype == 'invite_uid') {
				$where [$ktype] = intval ( $keyword );
			} else {
				$where [$ktype . ' LIKE'] = "%{$keyword}%";
			}
		}
		$role_id = irqst ( 'M_role_id' );
		if ($role_id) {
			$ex = dbselect ( 'MHR.user_id' )->from ( '{user_has_role} AS MHR' )->where ( array ('MHR.user_id' => imv ( 'U.user_id' ),'MHR.role_id' => $role_id ) );
			$where ['@'] = $ex;
		}
		$rows->where ( $where );
		$total = '';
		if ($_ct) {
			$total = $rows->count ( 'user_id' );
		}
		$data = array ('total' => $total,'rows' => $rows );
		$data ['canEditUser'] = icando ( 'u:account/user' );
		return view ( 'user/data.tpl', $data );
	}
	/**
	 * 保存用户角色.
	 *
	 * @param int $user_id
	 * @param array $roles
	 */
	private function _saveRoles($user_id, $roles) {
		if ($user_id) {
			dbdelete ()->from ( '{user_has_role}' )->where ( array ('user_id' => $user_id ) )->exec ();
			if (! empty ( $roles )) {
				$datas = array ();
				foreach ( $roles as $role_id ) {
					$datas [] = array ('user_id' => $user_id,'role_id' => $role_id,'sort' => 0 );
				}
				dbinsert ( $datas, true )->into ( '{user_has_role}' )->exec ();
				$roleNames = dbselect ( 'role_name' )->from ( '{user_role}' )->where ( array ('role_id IN' => $roles ) )->toArray ( 'role_name' );
				$roleName = implode ( ',', $roleNames );
				dbsave ( array ('user_id' => $user_id,'meta_name' => 'roles','meta_value' => $roleName ), array ('user_id' => $user_id,'meta_name' => 'roles' ), 'meta_id' )->into ( '{user_meta}' )->save ();
			}
		}
	}
}