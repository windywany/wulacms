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
}