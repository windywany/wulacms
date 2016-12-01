<?php

class BlackController extends Controller {
	protected $checkUser = true;
	protected $acls      = array('index' => 'm:account/member', 'data' => 'm:account/member', 'add' => 'c:account/member', 'edit' => 'u:account/member', 'save' => 'mid|u:account/member;c:account/member', 'del' => 'd:account/member');

	public function index() {
		$data = array();

		return view('black/index.tpl', $data);
	}

	public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {
		$rows = dbselect('*')->from('{member_nickname_black}')->limit(($_cp - 1) * $_lt, $_lt);
		$rows->sort($_sf, $_od);
		$keyword = rqst('keyword');
		$where   = new \Condition();
		if ($keyword) {
			$where ['nickname LIKE'] = "%{$keyword}%";
		}
		$rows->where($where);
		$total = '';
		if ($_ct) {
			$total = $rows->count('id');
		}
		$data                   = array('total' => $total, 'rows' => $rows->toArray());
		$data ['canEditMember'] = icando('u:account/member');
		$data ['canDelMember']  = icando('d:account/member');
		$data ['canAddMember']  = icando('a:account/member');

		return view('black/data.tpl', $data);
	}

	/**
	 * 新增用户.
	 *
	 * @return SmartyView
	 */
	public function add() {
		$rs               = array();
		$form             = new \passport\forms\NicknameForm();
		$data             = $rs;
		$data ['widgets'] = new DefaultFormRender ($form->buildWidgets($rs));
		$data ['rules']   = $form->rules();

		return view('black/form.tpl', $data);
	}

	public function del($ids = '') {
		$ids = safe_ids2($ids);
		if (!empty ($ids)) {
			if (dbdelete()->from('{member_nickname_black}')->where(array('id IN ' => $ids))->exec(true)) {
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
		$form = new \passport\forms\NicknameForm();
		$rs   = $form->valid();
		if ($rs) {
			unset ($rs ['id']);
			$rs ['update_time'] = time();
			$rs ['update_uid']  = $this->user->getUid();
			$rs ['create_time'] = time();
			$rs ['create_uid']  = $this->user->getUid();
			$nickname           = $rs['nickname'];
			if ($nickname) {
				$nicknames = explode("\n", $nickname);
				foreach ($nicknames as $n) {
					$n = trim($n);
					if ($n) {
						$rs['nickname'] = $n;
						dbinsert($rs)->into('{member_nickname_black}')->exec();
					}
				}
			}

			return NuiAjaxView::click('#btn-rtn-member', '保存成功.');
		} else {
			return NuiAjaxView::validate('MemberModelForm', '保存会员出错啦,数据校验失败!', $form->getErrors());
		}
	}

}