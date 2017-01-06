<?php
namespace finance\models;

use db\model\Model;
use finance\bill\DepositBill;
use pay\classes\IOrderHandler;
use pay\classes\OrderHandlerManager;

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/9/6
 * Time: 9:53
 */
class MemberDepositRecordModel extends Model {

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
		$query = $this->select('DPT.*,M.nickname', 'DPT')->join('{member} AS M', 'DPT.mid = M.mid')->where($where);
		$query->sort($_sf, $_od);
		$query->limit(($_cp - 1) * $_lt, $_lt);

		$data ['total'] = $query->count('DPT.id');

		$data ['rows'] = $query->toArray();

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
	 * 获取处理过的类型数组
	 * @return  array
	 */
	public function get_type_arr() {
		$types = $this->get_all();
		$ptype = [];
		foreach ($types as $row) {
			$ptype[ $row['id'] ] = $row['name'];
		}

		return $ptype;
	}

	/**
	 * 充值.
	 *
	 * @param \finance\bill\DepositBill $bill
	 *
	 * @return bool|int
	 */
	public function deposit(DepositBill $bill) {
		$data = $bill->toArray();
		//开始事务
		start_tran();
		$order = $this->select('status,amount,order_type')->where(['id' => $bill->id])->forupdate();
		if (!$order) {
			log_error('no finance order found - ' . $bill->id, 'deposit');
			// 订单不存在.
			rollback_tran();

			return false;
		}
		$status = intval($order['status']);
		if ($status !== 0) {
			//已经处理过了或者超时
			rollback_tran();

			return true;
		}
		if (floatval($order['amount']) != floatval($data['amount'])) {
			//错误的金额
			log_error('wrong  amount - ' . $bill->orderid, 'deposit');
			rollback_tran();

			return false;
		}
		$data['status']    = 1;
		$data['confirmed'] = time();
		//创建充值记录
		$rst = $this->update($data, ['id' => $bill->id]);
		if ($rst) {
			//提交事务
			if (commit_tran()) {
				//更新账户余额,需要在事务中执行.
				$order    = $this->select('*')->where(['id' => $bill->id])->get(0);
				$faccount = new MemberFinanceAccountModel();
				start_tran();
				$rst = $faccount->updateBalance($order['mid'], $order['amount'], $bill->id);
				if ($rst) {
					$orderType = $order['order_type'];
					$handler   = OrderHandlerManager::getHandler($orderType);
					if ($handler instanceof IOrderHandler) {
						$rst = $handler->onSuccess($order);
					}
					if ($rst) {
						commit_tran();
					} else {
						rollback_tran();
					}
				} else {
					rollback_tran();
				}

				return true;
			} else {
				log_error(var_export($data, true), 'deposit');
			}
		} else {
			log_error(var_export($data, true), 'deposit');
			log_error(var_export($this->errors, true), 'deposit');
		}
		rollback_tran();
		$orderType = $order['order_type'];
		$handler   = OrderHandlerManager::getHandler($orderType);
		if ($handler instanceof IOrderHandler) {
			$handler->onFailure($data, $this->errors);
		}

		return false;
	}

	/**
	 * 创建一新的订单.
	 *
	 * @param DepositBill $bill 新的充值订单.
	 *
	 * @return int  订单ID.
	 */
	public function newDepositOrder(DepositBill $bill) {
		$data                = $bill->toArray();
		$data['create_time'] = time();

		return $this->create($data);
	}

	/**
	 * 确认充值订单.
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function confirmOrder($id) {
		return $this->update(['status' => 3, 'order_confirmed' => time()], $id);
	}

	/**
	 * 充值对账.
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function checkOrder($id) {
		return $this->update(['status' => 4, 'checked' => time()], $id);
	}

	protected function config() {
		$this->rules['mid']             = ['required' => '请填写会员编号', 'digits' => '只能是数字'];
		$this->rules['order_type']      = ['required' => '请填写订单类型', 'maxlength(20)' => '最多20个字符'];
		$this->rules['subject']         = ['required' => '请填写订单名称', 'maxlength(128)' => '最多128个字符'];
		$this->rules['amount']          = ['required' => '请填写订单金额', 'num' => '只能是数字'];
		$this->rules['orderid']         = ['digits' => '只能是数字'];
		$this->rules['order_confirmed'] = ['digits' => '只能是数字'];
		$this->rules['confirmed']       = ['digits' => '只能是数字'];
	}
}