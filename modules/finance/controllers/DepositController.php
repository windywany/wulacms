<?php
namespace finance\controllers;

use finance\models\MemberDepositRecordModel;

/**
 *后台积分类型
 * User: FLy
 * Date: 2016/6/27
 * Time: 11:59
 */
class DepositController extends \Controller {
	protected $checkUser = true;

	//申请中
	public function index($member_id = 0) {
		$data = array();
		$uid  = intval($member_id);
		if ($uid > 0) {
			$data['uid'] = $uid;
		}

		return view('deposit/index.tpl', $data);
	}

	public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {
		$id                   = irqst('id');
		$uid                  = irqst('uid');
		$orderid              = irqst('orderid');
		$transid              = rqst('transid');
		$status               = rqst('status');
		$start                = trim(rqst('bd', ''));
		$end                  = trim(rqst('sd', '')) . '23:59:59';
		$where['DPT.deleted'] = 0;

		if ($id) {
			$where['DPT.id'] = $id;
		} else {
			if ($uid) {
				$where ['DPT.mid '] = $uid;
			}
			if ($orderid) {
				$where ['orderid'] = $orderid;
			}
			if ($transid) {
				$where ['transid'] = $transid;
			}
			if (is_numeric($status) && in_array($status, [0, 1, 2, 3, 4, 5])) {
				$where['DPT.status'] = $status;
			}
			if ($start != '') {
				$where ['DPT.create_time >='] = strtotime($start);
			} else {
				$where ['DPT.create_time >='] = strtotime('-7 days');
			}
			if ($end != '') {
				$where ['DPT.create_time <'] = strtotime($end);
			}
		}

		$where['_cp'] = $_cp;
		$where['_lt'] = $_lt;
		$where['_sf'] = $_sf;
		$where['_od'] = $_od;
		$where['_ct'] = $_ct;

		$pa      = new  MemberDepositRecordModel();
		$account = $pa->get_page_data($where);

		$data ['total'] = $account['total'];
		$data ['rows']  = $account['rows'];
		$data['status'] = ['0' => '待付款', '1' => '已付款', '2' => '已入账', '3' => '已处理', '4' => '已对账', '5' => '已作废'];

		//类型
		return view('deposit/index_data.tpl', $data);
	}
}