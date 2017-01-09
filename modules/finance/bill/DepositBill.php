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
/**
 * 充值账单.
 * @package finance\bill
 */
class DepositBill extends \ParameterDef {
	public $id;//充值订单编号
	public $mid;//会员编号
	public $order_type;//订单类型
	public $orderid;//系统订单ID
	public $order_confirmed;//订单处理器确认时间
	public $amount;//充值金额
	public $platform;//充值平台
	public $platformid;//充值平台ID
	public $transid;//第三方交易ID
	public $account;//充值账户
	public $confirmed;//入账时间
	public $subject;//项目
	public $device;//设备
	public $channel;//推广渠道
	public $note;//备注说明
}