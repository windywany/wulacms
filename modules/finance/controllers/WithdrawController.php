<?php
namespace finance\controllers;
/**
 *后台积分类型
 * User: FLy
 * Date: 2016/6/27
 * Time: 11:59
 */
class WithdrawController extends \Controller {
	protected $checkUser = true;

	//申请中
	public function index($status = 0) {
		$data = array();
		if (in_array($status, [0, 1, 2, 3])) {
			$data['status'] = intval($status);
		}

		return view('withdraw/index.tpl', $data);
	}

	public function change($op = '', $id = 0) {
		$id = intval($id);
		$op = trim($op);
		if (!$id) {
			return \NuiAjaxView::error('参数错误！');
		}

		$op_ok = ['pass' => 1, 'refuse' => 2, 'pay' => 3, 'rename' => 5, 'reopenid' => 6];

		if (!isset($op_ok[ $op ])) {
			return \NuiAjaxView::error('参数错误！');
		}
		$wd_mod = new \finance\models\MemberWithdrawRecordModel();
		$res    = $wd_mod->get_one(['id' => $id]);
		if (empty ($res)) {
			return \NuiAjaxView::error('不存在该数据！');
		}

		if ($res ['status'] == 2 && ($op == 'rename' || $op == 'reopenid')) {
			$note = array('rename' => '未实名认证', 'reopenid' => '微信openid异常');
			$ret  = $wd_mod->update(['approve_message' => $note[ $op ]], ['id' => $id]);
			if ($ret) {
				return \NuiAjaxView::error('备注成功！');
			} else {
				return \NuiAjaxView::error('操作失败！');
			}
		}

		$ret = $wd_mod->update(['status' => $op_ok[ $op ], 'approve_uid' => $this->user['uid'], 'approve_time' => time()], ['id' => $id]);
		if ($ret) {
			return \NuiAjaxView::refresh('修改成功！');
		} else {
			return \NuiAjaxView::error('操作失败！');
		}
	}

	public function del($id = 0) {
		$id = intval($id);

		if (empty($id)) {
			return \NuiAjaxView::error('参数错误！');
		}
		$res = dbselect('*')->from('{member_withdraw_type}')->where(array('id' => $id, 'deleted' => 0))->get(0);

		if (empty ($res)) {
			return \NuiAjaxView::error('不存在该数据！');
		}
		if ($res ['deleted'] != 0) {
			return \NuiAjaxView::error('废弃状态 数据无法操作！');
		}

		$ret = dbupdate('member_withdraw_type')->set(['deleted' => 1])->where(['id' => $id])->exec();
		if ($ret) {
			return \NuiAjaxView::refresh('修改成功！');
		} else {
			return \NuiAjaxView::error('删除失败！');
		}
	}

	public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {

		$transid = rqst('transid');
		$uid     = rqst('uid');
		$status  = rqst('status');

		if ($uid) {
			$where ['mid '] = $uid;
		}

		if (in_array($status, [0, 1, 2, 3])) {
			$where ['status'] = $status;
		}

		if ($transid) {
			$where ['transid'] = $transid;
		}

		$where['_cp'] = $_cp;
		$where['_lt'] = $_lt;
		$where['_sf'] = $_sf;
		$where['_od'] = $_od;
		$where['_ct'] = $_ct;

		$pa      = new \finance\models\MemberWithdrawRecordModel();
		$account = $pa->get_page_data($where);

		$data ['total'] = $account['total'];
		$data ['rows']  = $account['rows'];

		//类型
		return view('withdraw/index_data.tpl', $data);
	}

	public function refuse($wid = 0) {
		$wid = intval($wid);
		if (isset($_POST) AND !empty($_POST)) {
			$ret    = false;
			$op     = rqst('op');
			$wd_mod = new \finance\models\MemberWithdrawRecordModel();
			$res    = $wd_mod->get_one(['id' => $wid]);
			if (empty ($res)) {
				return \NuiAjaxView::error('不存在该数据！');
			}

			if ($res ['status'] == 0) {
				$note = array('rename' => '未实名认证', 'reopenid' => '微信openid异常');
			} else {
				return \NuiAjaxView::error('状态异常,无法操作！');
			}

			$ret = $wd_mod->update(['status' => '2', 'approve_message' => $note[ $op ], 'approve_uid' => $this->user['uid'], 'approve_time' => time()], ['id' => $wid]);
			if ($ret) {
				return \NuiAjaxView::refresh('添加成功！');
			} else {
				return \NuiAjaxView::error('操作失败！');
			}
		} else {

			$data        = array();
			$data['wid'] = $wid;

			return view('withdraw/refuse.tpl', $data);
		}
	}
}


