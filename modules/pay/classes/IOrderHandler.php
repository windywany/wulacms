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

interface IOrderHandler {
	/**
	 * 订单支付完成回调.
	 *
	 * @param string $order
	 *
	 * @return bool.
	 */
	public function onSuccess($order);

	/**
	 * @param array $data
	 * @param array $errors
	 */
	public function onFailure($data, $errors);

	/**
	 * 获取订单信息.
	 *
	 * @param $orderid
	 *
	 * @return array 需要包含以下字段
	 *               'amount'=>'订单金额'
	 *               'subject'=>'项目'
	 *               'mid'=>'用户编号'
	 */
	public function getOrder($orderid);
}