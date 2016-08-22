<?php
class MembersController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('index' => 'm:account/member','data' => 'm:account/member','add' => 'c:account/member','edit' => 'u:account/member','save' => 'mid|u:account/member;c:account/member','del' => 'd:account/member' );
	public function index($type = '') {
		$data = array ();
		$data ['status'] = array ('' => '-状态-','1' => '正常','0' => '禁用','2' => '未激活','3' => '待激活' );
		$data ['enable_auth'] = bcfg ( 'enable_auth@passport' );
		if ($data ['enable_auth']) {
			$data ['auth_status'] = array ('' => '-认证-','0' => '未认证','1' => '待审核','2' => '已认证','3' => '认证失败' );
		}
		$data ['status'] = 1;
		$data ['groups'] = array ('' => '-会员组-' );
		dbselect ()->from ( '{user_group}' )->treeWhere ( array ('type' => 'vip' ) )->treeWhere ( array ('type' => 'vip' ) )->treeOption ( $data ['groups'], 'group_id', 'upid', 'group_name' );
		$data ['types'] = MemberModelForm::getMemberTypes ();
		$data ['type'] = $type;
		if ($type) {
			$data ['memberTypeTitle'] = $data ['types'] [$type];
		} else {
			$data ['memberTypeTitle'] = '会员通行证';
		}
		$data ['all_roles'] = dbselect ( 'role_id,role_name' )->from ( '{user_role}' )->where ( array ('type' => 'vip' ) )->toArray ( 'role_name', 'role_id', array ('' => '-角色-' ) );
		$data ['canDelMember'] = icando ( 'd:account/member' );
		$data ['canAddMember'] = icando ( 'c:account/member' ) && ! bcfg ( 'connect_to@passport' );
		$data ['enable_invation'] = bcfg ( 'enable_invation@passport' );
		
		return view ( 'members/index.tpl', $data );
	}
	/**
	 * 新增用户.
	 *
	 * @return SmartyView
	 */
	public function add($type = '') {
		if (bcfg ( 'connect_to@passport' )) {
			Response::showErrorMsg ( '无权新增会员' );
		}
		$data = array ();
		$data ['groups'] = array ('' => '--请选择用户组--' );
		dbselect ()->from ( '{user_group}' )->treeWhere ( array ('type' => 'vip' ) )->treeOption ( $data ['groups'], 'group_id', 'upid', 'group_name' );
		$form = new MemberModelForm ( array ('mid' => 0 ) );
		$data ['rules'] = $form->rules ();
		$data ['status'] = '1';
		$data ['types'] = MemberModelForm::getMemberTypes ();
		$data ['type'] = $type;
		$widgets = MemberModelForm::getCustomeFields ( $form );
		if ($widgets) {
			$data ['widgets'] = new DefaultFormRender ( AbstractForm::prepareWidgets ( CustomeFieldWidgetRegister::initWidgets ( $widgets, array () ) ) );
		}
		// $data ['recommend_code'] = uniqid ( 'r' );
		$data ['enable_invation'] = bcfg ( 'enable_invation@passport' );
		$data ['enable_bind'] = bcfg ( 'enable_bind@passport' );
		$data ['roles'] = array ();
		$data ['all_roles'] = dbselect ( 'role_id,role_name' )->where ( array ('type' => 'vip' ) )->from ( '{user_role}' );
		$tpl = apply_filter ( 'get_member_form_template', 'members/form.tpl', $data );
		return view ( $tpl, $data );
	}
	/**
	 * 编辑用户.
	 *
	 * @param int $id
	 * @return NuiAjaxView SmartyView
	 */
	public function edit($id) {
		$id = intval ( $id );
		$user = dbselect ( '*' )->from ( '{member}' )->where ( array ('mid' => $id ) )->get ( 0 );
		if (empty ( $user )) {
			return NuiAjaxView::error ( '会员不存在.' );
		} else {
			$user ['groups'] = array ('' => '--请选择会员组--' );
			dbselect ()->from ( '{user_group}' )->treeWhere ( array ('type' => 'vip' ) )->treeOption ( $user ['groups'], 'group_id', 'upid', 'group_name' );
			$form = new MemberModelForm ( $user );
			$widgets = MemberModelForm::getCustomeFields ( $form );
			if ($widgets) {
				$cdatas = apply_filter ( 'load_member_data', $user );
				$user ['widgets'] = new DefaultFormRender ( AbstractForm::prepareWidgets ( CustomeFieldWidgetRegister::initWidgets ( $widgets, $cdatas ) ) );
			}
			$user ['roles'] = dbselect ( 'role_id' )->from ( '{member_has_role}' )->where ( array ('mid' => $id ) )->toArray ( 'role_id' );
			$user ['all_roles'] = dbselect ( 'role_id,role_name' )->where ( array ('type' => 'vip' ) )->from ( '{user_role}' );
			
			$form->removeRlue ( 'passwd', 'required' );
			
			$user ['types'] = MemberModelForm::getMemberTypes ();
			$user ['rules'] = $form->rules ( true );
			$user ['enable_invation'] = bcfg ( 'enable_invation@passport' );
			$user ['enable_bind'] = bcfg ( 'enable_bind@passport' );
			$tpl = apply_filter ( 'get_member_form_template', 'members/form.tpl', $user );
			return view ( $tpl, $user );
		}
	}
	public function del($uids) {
		$uids = safe_ids ( $uids, ',', true );
		if (! empty ( $uids )) {
			$data ['deleted'] = 1;
			$data ['update_time'] = time ();
			$data ['update_uid'] = $this->user->getUid ();
			if (dbupdate ( '{member}' )->set ( $data )->where ( array ('mid IN' => $uids ) )->exec ()) {
				$recycle = new DefaultRecycle ( $uids, 'Member', 'member', '会员编号:{mid}; 用户名:{username}', 'mid' );
				RecycleHelper::recycle ( $recycle );
				fire ( 'on_delete_member', $uids );
				ActivityLog::warn ( 'delete users:%s', implode ( ',', $uids ) );
				return NuiAjaxView::ok ( '已删除', 'click', '#refresh' );
			} else {
				return NuiAjaxView::error ( '数据库操作失败.' );
			}
		} else {
			Response::showErrorMsg ( '错误的编号', 404 );
		}
	}
	/**
	 * 保存.
	 */
	public function save() {
		if (bcfg ( 'connect_to@passport' )) {
			Response::showErrorMsg ( '此通行证已接入远征通行证,所有无法新增会员.' );
		}
		$form = new MemberModelForm ();
		MemberModelForm::getCustomeFields ( $form );
		$mid = irqst ( 'mid' );
		if ($mid) {
			$form->removeRlue ( 'passwd', 'required' );
		}
		$user = $form->valid ();
		if ($user) {
			$enable_invation = bcfg ( 'enable_invation@passport' );
			unset ( $user ['passwd1'] );
			unset ( $user ['mid'] );
			if (empty ( $user ['group_id'] )) {
				$user ['group_id'] = 0;
			}
			if (empty ( $user ['role_id'] )) {
				$user ['role_id'] = 0;
			}
			if ($enable_invation) {
				if ($user ['invite_code']) {
					$user ['invite_mid'] = dbselect ()->from ( '{member}' )->where ( array ('recommend_code' => $user ['invite_code'] ) )->get ( 'mid' );
				}
				if (empty ( $user ['invite_code'] )) {
					$user ['invite_mid'] = 0;
				}
			} elseif (empty ( $user ['invite_code'] )) {
				$user ['invite_mid'] = 0;
			}
			if (! empty ( $user ['passwd'] )) {
				$user ['passwd'] = md5 ( $user ['passwd'] );
			} else {
				unset ( $user ['passwd'] );
			}
			if (empty ( $user ['nickname'] )) {
				$user ['nickname'] = $user ['username'];
			}
			if (! empty ( $user ['user_passwd'] )) {
				$user ['user_passwd'] = md5 ( $user ['user_passwd'] );
			} else {
				unset ( $user ['user_passwd'] );
			}
			$user ['update_time'] = time ();
			$user ['update_uid'] = $this->user->getUid ();
			start_tran ();
			if (empty ( $mid )) {
				// 新增
				$user ['registered'] = time ();
				$user ['ip'] = $_SERVER ['REMOTE_ADDR'];
				$rst = dbinsert ( $user )->into ( '{member}' )->exec ();
				if ($rst) {
					$mid = $rst [0];
				}
			} else {
				// 修改
				$rst = dbupdate ( '{member}' )->set ( $user )->where ( array ('mid' => $mid ) )->exec ();
			}
			if ($rst) {
				$otype = rqst ( 'otype' );
				if ($otype != $user ['type']) {
					$user ['auth_status'] = $data ['auth_status'] = '0';
					$rst = dbupdate ( '{member}' )->set ( $data )->where ( array ('mid' => $mid ) )->exec ();
				}
				$user ['mid'] = $mid;
				$rst = apply_filter ( 'after_member_save', $user );
				if ($rst) {
					$roles = rqst ( 'roles', array () );
					MemberModelForm::saveRoles ( $mid, $roles );
					commit_tran ();
					return NuiAjaxView::ok ( '成功保存会员', 'click', '#btn-rtn-member' );
				} else {
					rollback_tran ();
					return NuiAjaxView::error ( '插件保存数据时出错啦.' );
				}
			} else {
				rollback_tran ();
				return NuiAjaxView::error ( '保存用户出错啦:数据库操作失败.' );
			}
		} else {
			return NuiAjaxView::validate ( 'MemberModelForm', '保存会员出错啦,数据校验失败!', $form->getErrors () );
		}
	}
	/**
	 * 会员数据.
	 *
	 * @param int $_cp
	 * @param int $_lt
	 * @param string $_sf
	 * @param string $_od
	 * @param int $_ct
	 * @return SmartyView
	 */
	public function data($_cp = 1, $_lt = 20, $_sf = 'mid', $_od = 'd', $_ct = 0) {
		$rows = dbselect ( 'M.*,UG.group_name,UG.group_refid AS `group`,RM.nickname as nickname1,RM.username AS username1,MM.value AS roles' )->from ( '{member} AS M' )->limit ( ($_cp - 1) * $_lt, $_lt );
		$rows->join ( '{user_group} AS UG', 'M.group_id = UG.group_id' );
		$rows->join ( '{member} AS RM', 'M.invite_mid = RM.mid' );
		$rows->join ( '{member_meta} AS MM', "M.mid = MM.mid AND MM.name = 'roles'" );
		$rows->sort ( $_sf, $_od );
		$where = Condition::where ( 'M.group_id', 'M.status', 'M.auth_status', 'M.type' );
		$where ['M.deleted'] = 0;
		$ktype = rqst ( 'ktype', 'username' );
		$keyword = rqst ( 'keyword' );
		if ($keyword) {
			if ($ktype == 'mid' || $ktype == 'invite_mid') {
				$where ['M.' . $ktype] = intval ( $keyword );
			} else {
				$where ['M.' . $ktype . ' LIKE'] = "%{$keyword}%";
			}
		}
		$role_id = irqst ( 'M_role_id' );
		if ($role_id) {
			$ex = dbselect ( 'MHR.mid' )->from ( '{member_has_role} AS MHR' )->where ( array ('MHR.mid' => imv ( 'M.mid' ),'MHR.role_id' => $role_id ) );
			$where ['@'] = $ex;
		}
		$rows->where ( $where );
		$total = '';
		if ($_ct) {
			$total = $rows->count ( 'M.mid' );
		}
		$data = array ('total' => $total,'rows' => $rows );
		$data ['canEditMember'] = icando ( 'u:account/member' );
		$data ['canDelMember'] = icando ( 'd:account/member' );
		$data ['canAuthMember'] = icando ( 'a:account/member' );
		
		$data ['types'] = MemberModelForm::getMemberTypes ();
		$data ['enable_auth'] = bcfg ( 'enable_auth@passport' );
		if ($data ['enable_auth']) {
			$data ['auth_api_url'] = apply_filter ( 'get_vip_auth_api_url', '' );
			$data ['auth_status'] = array ('0' => '<span class="label label-default">未认证</span>','1' => '<span class="label label-warning">待审核</span>','2' => '<span class="label label-success">已认证</span>','3' => '<span class="label label-danger">认证失败</span>' );
		}
		$data ['enable_invation'] = bcfg ( 'enable_invation@passport' );
		return view ( 'members/data.tpl', $data );
	}
}