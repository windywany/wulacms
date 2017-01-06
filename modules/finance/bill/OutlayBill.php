<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace finance\bill;

class OutlayBill extends \ParameterDef {
	public $id;//充值订单编号
	public $mid;//会员编号
	public $order_type = '';//订单类型
	public $orderid    = 0;//订单ID
	public $amount;//消息金额
	public $subject;//项目
	public $note;//备注说明
}