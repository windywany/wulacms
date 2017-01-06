<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace pay\controllers;

use finance\models\MemberDepositRecordModel;
use pay\classes\PayChannelManager;

class PayController extends \NonSessionController {

	public function index($channel, $id, $ajax = '') {

		$pay = PayChannelManager::getChannel($channel);

		if (!$pay) {
			if ($ajax) {
				return ['success' => false, 'msg' => '支付通道不存在'];
			}
			\Response::respond(500);
		}

		$id = intval($id);

		if (empty($id)) {
			if ($ajax) {
				return ['success' => false, 'msg' => '充值订单编号为空'];
			}
			\Response::respond(500);
		}

		$deposit = new MemberDepositRecordModel();

		$order = $deposit->get($id);

		if ($order) {
			$deposit->update(['platform' => $pay->getName()], $id);
			$form = $pay->getPayForm($order);
			if ($ajax) {
				return ['success' => true, 'form' => $form];
			}

			return view('pay.tpl', ['form' => $form]);
		} else {
			if ($ajax) {
				return ['success' => false, 'msg' => '充值订单不存在'];
			}
			\Response::respond(500);
		}
	}
}