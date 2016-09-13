<?php
namespace sms\controllers;

use sms\classes\Sms;

class SmsController extends \Controller {
	protected $checkUser = true;
	protected $acls      = ['*' => 'r:cms', 'save' => 'u:cms'];

	public function index() {
		$vendors = Sms::vendors();
		$data = ['vendors'=>[''=>'全部通道']];
		foreach ($vendors as $v=>$vendor){
			$data['vendors'][$v] = $vendor->getName();
		}
		return view('index.tpl',$data);
	}

	public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {
		$phone = rqst('phone');
		$time  = rqst('time');
		$time1  = rqst('time1');
		$vendor = rqst('vendor');
		$where = [];
		if($vendor){
			$where['vendor'] = $vendor;
		}
		if ($phone) {
			$where['phone LIKE'] = '%' . $phone . '%';
		}
		if ($time) {
			$s_time                  = strtotime($time.' 00:00:00');
			$where['create_time >='] = $s_time;
		}
		if ($time1) {
			$e_time                  = strtotime($time1.' 23:59:59');
			$where['create_time <='] = $e_time;
		}
		$data['rows']  = dbselect('*')->from('{sms_log}')->where($where)->sort($_sf, $_od)->limit(($_cp - 1) * $_lt, $_lt);
		$data['total'] = 0;
		if ($_ct) {
			$data['total'] = $data['rows']->count('id');
		}

		return view('data.tpl', $data);
	}
}