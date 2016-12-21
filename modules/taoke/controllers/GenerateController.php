<?php

namespace taoke\controllers;

use taoke\classes\Createtbk;

class GenerateController extends \Controller {
	protected $checkUser = true;

	public function index() {

		return view('form.tpl');
	}

	public function save() {
		$logo    = rqst('logo', '');
		$text    = rqst('content', '');
		$url     = rqst('turl', '');
		$user_id = rqst('user_id', '');
		if ($text == '' || $url == '') {
			return ['status' => 1, 'msg' => '标题，链接，user_id不可为空'];
		}
		//创建淘口令
		$ctbk = new  Createtbk();
		$res  = $ctbk->create($text, $url, $user_id, $logo);

		return $res;
	}
}