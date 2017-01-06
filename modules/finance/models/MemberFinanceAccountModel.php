<?php
namespace finance\models;

use db\model\Model;

class MemberFinanceAccountModel extends Model {
	/*分类数*/
	public function get_page_data($cond = []) {
		$data = [];
		$_sf  = $cond['_sf'];
		$_od  = $cond['_od'];
		$_cp  = $cond['_cp'];
		$_lt  = $cond['_lt'];
		$_ct  = $cond['_ct'];
		unset($cond['_sf'], $cond['_od'], $cond['_cp'], $cond['_lt'], $cond['_ct']);

		$where = $cond;
		$query = $this->select('FA.*,M.nickname', 'FA')->join('{member} AS M', 'FA.mid = M.mid')->where($where);
		$query->sort($_sf, $_od);
		$query->limit(($_cp - 1) * $_lt, $_lt);

		$data ['total'] = $query->count('FA.id');
		$row            = [];
		foreach ($query as $item) {
			$row[] = $item;
		}
		$data ['rows'] = $row;

		return $data;
	}

	/**
	 * 据查询条件获取单条记录
	 *
	 * @param  array $cond
	 *
	 * @return  boolean $res
	 */
	public function get_one($cond = []) {
		$res = dbselect('*')->from($this->table)->where($cond)->get(0);

		return $res;
	}

	/**
	 * @param array $cond
	 *
	 * @return mixed
	 */
	public function get_all($cond = []) {
		$cond['id >'] = 0;
		$res          = dbselect('*')->from($this->table)->where($cond)->toArray();

		return $res;
	}

	/**
	 * 更新账户余额.
	 * 注意，此方法必须运行于事务中。
	 *
	 * @param string|int $mid     会员
	 * @param float      $amount  金额
	 * @param int        $orderid 充值订单编号
	 *
	 * @return bool
	 */
	public function updateBalance($mid, $amount, $orderid = 0) {
		if ($amount == 0) {
			return true;
		}
		$account = $this->getAccount($mid);
		if (!$account) {
			return false;
		}
		$id     = $account['id'];
		$amount = floatval($amount);
		// 加锁
		$maccount = $this->select('id')->where(['id' => $id])->forupdate();
		if (!$maccount) {
			//账户不存在
			return false;
		}
		if ($orderid) {
			$deposit = new MemberDepositRecordModel();
			if ($deposit->exist(['id' => $orderid, 'status >' => 1])) {
				//已经处理过了.
				return false;
			}
		}
		if ($amount >= 0) {
			$data['balance'] = imv('balance + ' . $amount);
			$data['amount']  = imv('amount + ' . $amount);
		} else {
			$data['balance'] = imv('balance - ' . abs($amount));
			$data['spend']   = imv('spend + ' . abs($amount));
		}
		$rst = $this->update($data, ['id' => $id]);
		if ($rst) {
			if ($orderid) {
				$deposit = new MemberDepositRecordModel();
				//将充值订单状态变为2-已入账
				if (!$deposit->update(['status' => 2], $orderid)) {
					return false;
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * 获取账户.
	 *
	 * @param string|int $mid
	 * @param bool       $lock 是否锁定.
	 *
	 * @return array 账户数组.
	 */
	public function getAccount($mid, $lock = false) {
		$account = $this->get(['mid' => $mid]);
		if (!$account) {
			$id = $this->createAccount($mid);
			if (!$id) {
				return [];
			}
		}
		if ($lock) {
			$account = $this->select('*')->where(['mid' => $mid])->forupdate();
		} elseif (!$account) {
			$account = $this->get(['mid' => $mid]);
		}

		return $account;
	}

	/**
	 * 创建账户.
	 * 注意，请在事务中调用此方法.
	 *
	 * @param string|int $mid
	 *
	 * @return int
	 */
	public function createAccount($mid) {
		$data['mid']         = $mid;
		$data['create_time'] = time();
		$data['update_time'] = $data['create_time'];

		return $this->create($data);
	}

	protected function config() {
		$this->rules['mid']         = ['required' => '请填写', 'digits' => '只能是数字'];
		$this->rules['create_time'] = ['required' => '请填写', 'digits' => '只能是数字'];
		$this->rules['update_time'] = ['digits' => '只能是数字'];
	}

}