<?php
/**
 * Created by PhpStorm.
 * DEC :
 * User: wangwei
 * Date: 2016/12/2
 * Time: 13:02
 */

namespace taoke\classes;

class Createtbk {
	public function create($text, $url, $user_id = '', $logo = '') {
		if ($text == '' || $url == '' || $user_id == '') {
			return ['status' => 1, 'msg' => '内容，url,uid必填'];
		}
		$appkey = cfg('appkey@taoke', '');
		$secret = cfg('appsecret@taoke', '');
		if ($appkey == '' || $secret == '') {
			return ['status' => 1, 'msg' => 'appkey和secretkey不可为空'];
		}
		date_default_timezone_set('Asia/Shanghai');
		$c               = new \TopClient();
		$c->appkey       = $appkey;
		$c->secretKey    = $secret;
		$req             = new \WirelessShareTpwdCreateRequest();
		$tpwd_param      = new \IsvTpwdInfo();
		if ($logo) {
			$tpwd_param->logo = $logo;
		}
		$tpwd_param->user_id = $user_id;
		$tpwd_param->text    = $text;
		$tpwd_param->url     = $url;
		$req->setTpwdParam(json_encode($tpwd_param));
		$resp = $c->execute($req);
		if ($resp->model) {
			//保存数据
			$data['logo']        = $logo;
			$data['url']         = $url;
			$data['content']     = $text;
			$data['user_id']     = $user_id;
			$data['token']       = $resp->model;
			$data['create_time'] = time();
			dbinsert($data)->into('{tbk_token}')->exec();

			return ['status' => 0, 'msg' => $resp->model.''];
		} else {
			return ['status' => 1, 'msg' => $resp->sub_msg.''];
		}

	}
}