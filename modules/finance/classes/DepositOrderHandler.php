<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace finance\classes;

use finance\models\MemberDepositRecordModel;
use pay\classes\IOrderHandler;

class DepositOrderHandler implements IOrderHandler {
	public function onSuccess($order) {
		//更新订单确认时间
		$deposit = new MemberDepositRecordModel();

		return $deposit->confirmOrder($order['id']);
	}

	public function onFailure($data, $errors) {
		$rtn['data']  = $data;
		$rtn['error'] = $errors;
		log_error(var_export($rtn), 'deposit');
	}

	/**
	 * @param $orderid
	 *
	 * @return null
	 */
	public function getOrder($orderid) {
		return null;
	}

	public static function get_desposit_order_handlers($hs) {
		$hs['deposit'] = new self();

		return $hs;
	}
}