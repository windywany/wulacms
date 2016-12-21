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
		if ($text == '' || $url == '') {
			return ['status' => 1, 'msg' => '内容，url,uid必填'];
		}
		$appkey = cfg('appkey@taoke', '');
		$secret = cfg('appsecret@taoke', '');
		if ($appkey == '' || $secret == '') {
			return ['status' => 1, 'msg' => 'appkey和secretkey不可为空'];
		}
		date_default_timezone_set('Asia/Shanghai');
		$c            = new \TopClient();
		$c->appkey    = $appkey;
		$c->secretKey = $secret;
		$req          = new \WirelessShareTpwdCreateRequest();
		$tpwd_param   = new \IsvTpwdInfo();
		if ($logo) {
			$tpwd_param->logo = $logo;
		}
		if (!$user_id) {
			$user_id = $tpwd_param->user_id = cfg('user_id@taoke', '24234234234');
		} else {
			$tpwd_param->user_id = $user_id;
		}

		$tpwd_param->text = $text;
		$tpwd_param->url  = $url;
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

			return ['status' => 0, 'msg' => $resp->model . ''];
		} else {
			return ['status' => 1, 'msg' => $resp->sub_msg . ''];
		}

	}

	public function getText($data) {
		if ($data) {
			$data['title'] = mb_substr($data['title'], 0, 14);
			$tpl           = cfg('tpl@taoke', '{title}原价{price}元，抢券立省{discount}元');
			$rep_arr       = ['platform', 'title', 'price', 'real_price', 'token', 'page_id', 'conpou_price', 'discount', 'coupon_remain', 'coupon_stop', 'wangwang', 'shopname'];
			$res           = false;
			foreach ($rep_arr as $k) {
				$res = str_replace('{' . $k . '}', $data[ $k ], $tpl);
				$tpl = $res;
			}

			return $res;
		} else {
			return null;
		}
	}
}