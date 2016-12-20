<?php

class MembersController extends Controller {
	protected $checkUser = true;
	protected $acls      = array('index' => 'm:account/member', 'data' => 'm:account/member', 'add' => 'c:account/member', 'edit' => 'u:account/member', 'save' => 'mid|u:account/member;c:account/member', 'del' => 'd:account/member');

	public function index() {
		$data            = array();
		$data ['status'] = array('' => '-状态-', '1' => '正常', '0' => '禁用', '2' => '未激活', '3' => '待激活');
		$data ['status'] = 1;
		$data ['groups'] = array('' => '-会员组-');
		dbselect()->from('{user_group}')->treeWhere(array('type' => 'vip'))->treeWhere(array('type' => 'vip'))->treeOption($data ['groups'], 'group_id', 'upid', 'group_name');

		$data ['all_roles']       = dbselect('role_id,role_name')->from('{user_role}')->where(array('type' => 'vip'))->toArray('role_name', 'role_id', array('' => '-角色-'));
		$data ['canDelMember']    = icando('d:account/member');
		$data ['canAddMember']    = icando('c:account/member') && !bcfg('connect_to@passport');
		$data ['enable_invation'] = bcfg('enable_invation@passport');
		$data ['columns']         = apply_filter('get_member_columns', []);
		$fields                   = apply_filter('get_member_search_fields', []);

		if ($fields) {
			$gp          = 1;
			$col         = 0;
			$csearchForm = new DynamicForm ('CustomerMemberSearchForm');
			foreach ($fields as $n => $f) {
				if (!isset ($f ['col']) || !intval($f ['col'])) {
					$f ['col'] = 3;
				}
				$col += intval($f ['col']);
				if ($col > 12) {
					$gp += 1;
					$col = intval($f ['col']);
				}
				$f ['group'] = $gp;
				$csearchForm->addField($n, $f);
			}
			$data ['widgets'] = new DefaultFormRender ($csearchForm->buildWidgets(array()));
		}

		return view('members/index.tpl', $data);
	}

	/**
	 * 新增用户.
	 *
	 * @return SmartyView
	 */
	public function add() {
		if (bcfg('connect_to@passport')) {
			Response::showErrorMsg('无权新增会员');
		}
		$data            = array();
		$data ['groups'] = array('' => '--请选择用户组--');
		dbselect()->from('{user_group}')->treeWhere(array('type' => 'vip'))->treeOption($data ['groups'], 'group_id', 'upid', 'group_name');
		$form            = new MemberModelForm (array('mid' => 0));
		$data ['rules']  = $form->rules();
		$data ['status'] = '1';

		//$data ['recommend_code']  = uniqid('r');
		$data ['enable_invation'] = bcfg('enable_invation@passport');
		$data ['enable_bind']     = bcfg('enable_bind@passport');
		$data ['roles']           = array();
		$data ['all_roles']       = dbselect('role_id,role_name')->where(array('type' => 'vip'))->from('{user_role}');
		$tpl                      = 'members/form.tpl';

		return view($tpl, $data);
	}

	/**
	 * 编辑用户.
	 *
	 * @param int $id
	 *
	 * @return NuiAjaxView SmartyView
	 */
	public function edit($id) {
		$id    = intval($id);
		$model = new \passport\models\MemberModel();
		$user  = $model->get(['mid' => $id]);
		if (empty ($user)) {
			return NuiAjaxView::error('会员不存在.');
		} else {
			$user ['groups'] = array('' => '--请选择会员组--');
			dbselect()->from('{user_group}')->treeWhere(array('type' => 'vip'))->treeOption($user ['groups'], 'group_id', 'upid', 'group_name');
			$form = new MemberModelForm ($user);

			$user ['roles']     = dbselect('role_id')->from('{member_has_role}')->where(array('mid' => $id))->toArray('role_id');
			$user ['all_roles'] = dbselect('role_id,role_name')->where(array('type' => 'vip'))->from('{user_role}');
			if ($user['invite_mid']) {
				$user['invite_mid'] .= ':' . $model->getField('nickname', $user['invite_mid'], '');
			} else {
				$user['invite_mid'] = '';
			}
			$form->removeRlue('passwd', 'required');
			$user ['rules']           = $form->rules(true);
			$user ['enable_invation'] = bcfg('enable_invation@passport');
			$user ['enable_bind']     = bcfg('enable_bind@passport');
			$tpl                      = 'members/form.tpl';

			return view($tpl, $user);
		}
	}

	public function del($uids) {
		$uids = safe_ids($uids, ',', true);
		if (!empty ($uids)) {
			$data ['deleted']     = 1;
			$data ['update_time'] = time();
			$data ['update_uid']  = $this->user->getUid();
			if (dbupdate('{member}')->set($data)->where(array('mid IN' => $uids))->exec()) {
				$recycle = new DefaultRecycle ($uids, 'Member', 'member', '会员编号:{mid}; 用户名:{username}', 'mid');
				RecycleHelper::recycle($recycle);
				fire('on_delete_member', $uids);
				ActivityLog::warn('delete users:%s', implode(',', $uids));

				return NuiAjaxView::ok('已删除', 'click', '#refresh');
			} else {
				return NuiAjaxView::error('数据库操作失败.');
			}
		} else {
			Response::showErrorMsg('错误的编号', 404);
		}
	}

	/**
	 * 保存.
	 */
	public function save() {
		if (bcfg('connect_to@passport')) {
			Response::showErrorMsg('此通行证已接入远征通行证,所有无法新增会员.');
		}
		$form = new MemberModelForm ();
		$mid  = irqst('mid');
		if ($mid) {
			$form->removeRlue('passwd', 'required');
		}
		$user = $form->valid();
		if ($user) {
			unset ($user ['passwd1']);
			unset ($user ['mid']);
			if (!$mid || empty($user['salt'])) {
				$user['salt'] = rand_str(64);
			}
			if (empty ($user ['group_id'])) {
				$user ['group_id'] = 0;
			}
			if (!empty ($user ['passwd'])) {
				$user['passwd'] = MemberModelForm::generatePwd($user['passwd'], $user['salt']);
			} else {
				unset ($user ['passwd']);
			}
			if (empty ($user ['nickname'])) {
				$user ['nickname'] = $user ['username'];
			}

			$user ['update_time'] = time();
			$user ['update_uid']  = $this->user->getUid();
			$user ['invite_mid']  = (int)$user['invite_mid'];
			start_tran();
			if (empty ($mid)) {
				// 新增
				$user ['registered'] = time();
				$rst                 = dbinsert($user)->into('{member}')->exec();
				if ($rst) {
					$mid = $rst [0];
				}
			} else {
				// 修改
				$rst = dbupdate('{member}')->set($user)->where(array('mid' => $mid))->exec();
			}
			if ($rst) {
				$user ['mid'] = $mid;
				$rst          = apply_filter('after_member_save', $user);
				if ($rst) {
					$roles = rqst('roles', array());
					$model = new \passport\models\MemberMetaModel();
					$model->saveRoles($mid, $roles);
					commit_tran();

					return NuiAjaxView::ok('成功保存会员', 'click', '#btn-rtn-member');
				} else {
					rollback_tran();

					return NuiAjaxView::error('会员保存时出错啦.');
				}
			} else {
				rollback_tran();

				return NuiAjaxView::error('保存用户出错啦:数据库操作失败.');
			}
		} else {
			return NuiAjaxView::validate('MemberModelForm', '保存会员出错啦,数据校验失败!', $form->getErrors());
		}
	}

	/**
	 * 会员数据.
	 *
	 * @param int    $_cp
	 * @param int    $_lt
	 * @param string $_sf
	 * @param string $_od
	 * @param int    $_ct
	 *
	 * @return SmartyView
	 */
	public function data($_cp = 1, $_lt = 20, $_sf = 'mid', $_od = 'd', $_ct = 0) {
		$rows = dbselect('M.*,RM.nickname as nickname1,RM.username AS username1,MM.value AS roles')->from('{member} AS M')->limit(($_cp - 1) * $_lt, $_lt);
		$rows->join('{member} AS RM', "M.invite_mid = RM.mid");
		$rows->join('{member_meta} AS MM', "M.mid = MM.mid AND MM.name = 'roles'");
		$rows->sort($_sf, $_od);
		$where               = Condition::where('M.group_id', 'M.status');
		$where ['M.deleted'] = 0;
		$ktype               = rqst('ktype', 'username');
		$keyword             = rqst('keyword');
		if ($keyword) {
			if ($ktype == 'mid' || $ktype == 'invite_mid') {
				$where [ 'M.' . $ktype ] = intval($keyword);
			} else {
				$where [ 'M.' . $ktype . ' LIKE' ] = "%{$keyword}%";
			}
		}
		$role_id = irqst('M_role_id');
		if ($role_id) {
			$ex          = dbselect('MHR.mid')->from('{member_has_role} AS MHR')->where(array('MHR.mid' => imv('M.mid'), 'MHR.role_id' => $role_id));
			$where ['@'] = $ex;
		}
		$rows->where($where);
		$rows  = apply_filter('filter_members_query', $rows);
		$total = '';
		if ($_ct) {
			$total = $rows->count('M.mid');
		}

		$data                     = array('total' => $total, 'rows' => $rows->toArray());
		$data ['groups']          = dbselect('group_id,group_name')->from('{user_group}')->where(['type' => 'vip'])->toArray('group_name', 'group_id');
		$data ['canEditMember']   = icando('u:account/member');
		$data ['canDelMember']    = icando('d:account/member');
		$data ['canAuthMember']   = icando('a:account/member');
		$data ['enable_invation'] = bcfg('enable_invation@passport');
		$data ['columns']         = apply_filter('get_member_columns', []);

		return view('members/data.tpl', $data);
	}
}