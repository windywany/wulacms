<?php
/**
 * Created by PhpStorm.
 * DEC :
 * User: wangwei
 * Date: 2016/12/21
 * Time: 11:29
 */
class  GeturlController extends Controller {
	//推广跳转链接
	public function index($token) {
		if ($token) {
			$tbk_url = dbselect('coupon_url')->from('{tbk_goods}')->where(['token' => '￥'.$token.'￥'])->get('coupon_url');
			if ($tbk_url) {
				return ['status' => 0, 'url' => $tbk_url];
			} else {
				return ['status' => 1, 'url' => '暂无链接'];
			}
		} else {
			return ['status' => 1, 'url' => 'id不可为空'];
		}

	}
}