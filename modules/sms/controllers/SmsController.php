<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/9/7
 * Time: 10:29
 */

namespace sms\controllers;


class SmsController extends \Controller
{
	protected $checkUser = true;
	protected $acls = ['*'=>'r:cms','save'=>'u:cms'];

	public function index(){
		return view('index.tpl',[]);
	}
	public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0){
		 $phone = rqst('phone');
		 $time = rqst('time');
		$where = [];
		if($phone) {
			$where['phone LIKE']='%'.$phone.'%';
		}
		if($time) {
			$s_time = strtotime($time);
			$e_time = $s_time+3600*24;
			$where['create_time >=']=$s_time;
			$where['create_time <']=$e_time;
		}
		$where['id >'] =0;
		$data['rows'] = dbselect('*')->from('{sms_log}')->where($where)->sort($_sf,$_od)->limit(($_cp-1)*$_lt,$_lt);
		$data['total'] = 0;
		if($_ct){
			$data['total'] = $data['rows']->count('id');
		}
		return view('data.tpl',$data);
	}
}