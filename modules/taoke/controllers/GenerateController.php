<?php

namespace taoke\controllers;

use taoke\classes\Createtbk;

class GenerateController extends \Controller {
	public function index(){

		return view('form.tpl');
	}
	public function save(){
      $logo = rqst('logo','');
      $text = rqst('content','');
      $url = rqst('turl','');
      $user_id = rqst('user_id','');
	  //创建淘口令
      $ctbk = new  Createtbk();
	  $res = $ctbk->create($text,$url,$user_id,$logo);
	  return $res;
	}
}