<?php
namespace finance\controllers;

use finance\models\MemberFinanceAccountModel;

/**
 *后台积分类型
 * User: FLy
 * Date: 2016/6/27
 * Time: 11:59
 */
class FinanceController extends \Controller {
	protected $checkUser = true;

	//申请中
	public function index() {

		$data              = array();
		$data['pageTitle'] = '积分列表';

		return view('finance/index.tpl', $data);
	}

	public function del($id = 0) {
		$id = intval($id);

		if (empty($id)) {
			return \NuiAjaxView::error('参数错误！');
		}
		$res = dbselect('*')->from('{member_finance_type}')->where(array('id' => $id, 'deleted' => 0))->get(0);

		if (empty ($res)) {
			return \NuiAjaxView::error('不存在该数据！');
		}
		if ($res ['deleted'] != 0) {
			return \NuiAjaxView::error('废弃状态 数据无法操作！');
		}

		$ret = dbupdate('member_finance_type')->set(['deleted' => 1])->where(['id' => $id])->exec();
		if ($ret) {
			return \NuiAjaxView::refresh('修改成功！');
		} else {
			return \NuiAjaxView::error('删除失败！');
		}
	}

	public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {

		$name = rqst('uname');
		$uid  = rqst('uid');
		$type = rqst('type');

		if ($uid) {
			$where ['mid '] = $uid;
		} elseif ($name) {
			$where ['mname like'] = '%' . $name . '%';
		}

		if ($type) {
			$where ['type'] = $type;
		}

		$where['_cp'] = $_cp;
		$where['_lt'] = $_lt;
		$where['_sf'] = $_sf;
		$where['_od'] = $_od;
		$where['_ct'] = $_ct;
		$pa           = new  MemberFinanceAccountModel();
		$account      = $pa->get_page_data($where);

		$data ['total'] = $account['total'];
		$data ['rows']  = $account['rows'];

		//类型
		return view('finance/index_data.tpl', $data);
	}
}