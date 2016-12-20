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
		$data                    = array();
		$uid                     = intval($member_id);
		$data['pageTitle']       = '列表';
		$data['pay_success_num'] = cfg('pay_success_num@dashen', 0);
		$data['pay_total_money'] = cfg('pay_total_money@dashen', 0);
		//		$data[''] = $sum_sta
		$sum_data = dbselect('sum_money,device')->from('{dept_statistics}')->where(['type' => 0])->sort('device', 'desc')->toArray();
		$sum_sta  = [];
		/*andr,ios,oth,sum*/
		$list = ['1' => 'andr', '2' => 'ios', '3' => 'h5', '4' => 'oth', '0' => 'sum'];
		foreach ($sum_data as $row) {
			if (!isset($list[ $row['device'] ])) {
				continue;
			}
			$sum_sta[ $list[ $row['device'] ] ] = number_format($row['sum_money'], 0, '.', '');
		}
		$data['sum_sta'] = $sum_sta;
		if ($uid > 0) {
			$data['uid'] = $uid;
		}

		return view('deposit/index.tpl', $data);
	}

	public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {

		$uid       = rqst('uid');
		$orderid   = rqst('orderid');
		$transid   = rqst('transid');
		$confirmed = rqst('confirmed');
		$start     = trim(rqst('bd', ''));
		$end       = trim(rqst('sd', '')) . '23:59:59';
		if ($uid) {
			$where ['mid '] = $uid;
		}

		if ($orderid) {
			$where ['orderid'] = $orderid;
		}

		if ($transid) {
			$where ['transid'] = $transid;
		}
		if ($confirmed == 1) {
			$where ['confirmed >'] = 0;
		}
		if ($confirmed == 2) {
			$where ['confirmed '] = 0;
		}
		if ($start != '') {
			$where ['create_time >='] = strtotime($start);
		}
		if ($end != '') {
			$where ['create_time <'] = strtotime($end);
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

		//类型
		return view('deposit/index_data.tpl', $data);
	}
}