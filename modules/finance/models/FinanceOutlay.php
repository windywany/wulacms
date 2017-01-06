<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace finance\models;

use db\model\Model;
use finance\bill\OutlayBill;

class FinanceOutlay extends Model {
	public $table = 'member_finance_outlay';

	/**
	 * 增加一笔消息记录，它同时会更新账户余额。
	 *
	 * 此方法需要在事务中调用.
	 *
	 * @param \finance\bill\OutlayBill $bill
	 *
	 * @return bool
	 */
	public function outlay(OutlayBill $bill) {
		$account = new MemberFinanceAccountModel();
		$acc     = $account->getAccount($bill->mid, true);
		$data    = $bill->toArray();
		if (!$acc || $acc['balance'] < $bill->amount) {
			return false;
		}
		$data['create_time'] = time();
		if (isset($data['order_type'])) {
			$data['order_type'] = 'none';
			$data['orderid']    = 0;
		}
		$id = $this->create($data);
		if ($id) {
			$mid    = $bill->mid;
			$amount = $bill->amount;
			if ($account->updateBalance($mid, -$amount)) {
				return true;
			}
		}

		return false;
	}

	protected function config() {
		$this->rules['mid']        = ['required' => '请填写会员编号', 'digits' => '只能是数字'];
		$this->rules['order_type'] = ['required' => '请填写消息类型', 'maxlength(20)' => '最多20个字符'];
		$this->rules['subject']    = ['required' => '请填写消息名称', 'maxlength(128)' => '最多128个字符'];
		$this->rules['amount']     = ['required' => '请填写消息金额', 'num' => '只能是数字'];
		$this->rules['orderid']    = ['digits' => '只能是数字'];
	}
}