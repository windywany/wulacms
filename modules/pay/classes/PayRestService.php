<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace pay\classes;

class PayRestService {
	public static function on_init_rest_server(\RestServer $server) {
		$server->registerClass(new self(), '1', 'pay');

		return $server;
	}

	/**
	 * 支付请求.
	 *
	 * @param array  $param 参数
	 *                      orderid=>'订单编号'
	 *                      paychannel=>'支付方式'
	 *
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_post_pay($param, $key, $secret) {
		if (!isset($param['orderid'])) {
			return [''];
		}
	}

	/**
	 * 创建一个支付订单，并返回订单号
	 *
	 * @param array  $param 参数
	 *                      'order_type'=>'业务订单类型,必填',
	 *                      'amount'=>'金额，orderid为空时必填',
	 *                      'subject'=>'项目，orderid为空时必填',
	 *                      'mid'=>'用户编号,orderid为空时必真',
	 *                      'device'=>'设备，可选'
	 *                      'channel'=>'推广渠道，可选',
	 *                      'orderid'=>'业务订单编号，amount为空时，必填'
	 *
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_post_create_order($param, $key, $secret) {
		return [];
	}

	/**
	 * 支付复核.
	 *
	 * @param array  $param 参数：
	 *                      'orderid'=>'订单编号'
	 * @param string $key
	 * @param string $secret
	 *
	 * @return array
	 */
	public function rest_get_check($param, $key, $secret) {
		return [];
	}
}