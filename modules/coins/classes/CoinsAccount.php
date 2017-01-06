<?php
/**
 * @desc  .member_coins_account操作
 * @author: FLY
 * @Date  : 2016/9/13 17:53
 */

namespace coins\classes;

use coins\models\MemberCoinsAccountModel;
use coins\models\MemberCoinsRecordModel;

class CoinsAccount {
	/**
	 * 扣减金币明细
	 *
	 * @param array $data
	 *
	 * @return void
	 */
	public static function sub_coins($data = []) {
		$mid    = intval($data['mid']);
		$amount = intval($data['amount']);
		$list   = $data['list'];
		foreach ($list as $row) {
			$tmp_amount = intval($row['balance']);
			$break      = false;
			if ($amount > $tmp_amount) {
				$amount -= $tmp_amount;
				$tmp_amount = 0;
			} else {
				$tmp_amount -= $amount;
				$break = true;
			}

			$usr_coins_ac = new  MemberCoinsAccountModel();
			$ac_ret       = $usr_coins_ac->update(['balance' => $tmp_amount], ['mid' => $mid, 'type' => $row['type']]);

			$usr_coins_re           = new MemberCoinsRecordModel();
			$in_data['mid']         = $mid;
			$in_data['create_time'] = time();
			$in_data['type']        = $row['type'];
			$in_data['is_outlay']   = 1;
			$in_data['subject']     = '提现支出';
			$in_data['note']        = '提现支出减扣';
			$in_data['amount']      = $break ? $amount : $row['balance'];
			$u_c_r                  = $usr_coins_re->create($in_data);
			if ($break) break;
		}

	}

	public static function load_member_data_for_passport(\Passport $user) {
		$mid     = $user->getUid();
		$model   = new MemberCoinsAccountModel();
		$account = $model->getSummaryAccount($mid);
		if ($account) {
			$user['coins'] = $account['balance'];
		} else {
			$user['coins'] = 0;
		}
	}
}