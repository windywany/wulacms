<?php
namespace coins\controllers;
/**
 *后台积分类型
 * User: FLy
 * Date: 2016/6/27
 * Time: 11:59
 */
class CoinsController extends \Controller {
	protected $checkUser = true;

	public function index() {
		$type              = new \coins\models\MemberCoinsTypeModel();
		$data              = array();
		$data['pageTitle'] = '帐户列表';
		$data['types']     = $type->get_all();

		return view('coins/index.tpl', $data);
	}

	//后台添加积分
	public function addmoney() {
		$mid  = intval(rqst('mid'));
		$data = array();
		//类型分类
		$res = dbselect('*')->from('{member_coins_type}')->toArray();
		$row = [];
		foreach ($res as $re) {
			if ($re['type'] !== 'summary') {
				$row[] = $re;
			}
		}
		$data['type'] = $row;
		if (isset($_POST) AND !empty($_POST)) {
			if (empty($_POST['userid'])) {
				return \NuiAjaxView::error('填写用户id！');
			}
			if (empty($_POST['coins'])) {
				return \NuiAjaxView::error('填写用户金币！');
			}

			$register_coins = (int)$_POST['coins'];
			$type           = trim(rqst('type'));

			//查看是否已有积分
			$where        = array();
			$where['mid'] = $_POST['userid'];

			//查看用户是否存在
			$dbuser = dbselect('*')->from('{member}')->where($where)->get();
			if (empty($dbuser)) {
				return \NuiAjaxView::error('填写正确用户id！');
			}
			$coinsModel    = new \coins\models\MemberCoinsAccountModel ();
			$coinsLogModel = new \coins\models\MemberCoinsRecordModel ();
			// 系统赠送金币
			$inviteExist  = $coinsModel->init($_POST['userid'], $type);
			$summaryExist = $coinsModel->init($_POST['userid'], 'summary');
			// 送金币
			if ($register_coins) {
				if ($inviteExist && $summaryExist) {
					start_tran();
					$set ['amount']  = imv('amount+' . $register_coins);
					$set ['balance'] = imv('balance+' . $register_coins);
					$res             = $coinsModel->update($set, ['id' => $inviteExist]);

					$res                 = $res && $coinsModel->update($set, ['id' => $summaryExist]);
					$log                 = [];
					$log ['create_time'] = time();
					$log ['mid']         = $_POST['userid'];
					$log ['type']        = $type;
					$log ['amount']      = $register_coins;
					$log ['subject']     = $type;
					$log ['balance']     = $coinsModel->get_field($inviteExist, 'balance');
					$log ['note']        = '赠送金币';
					$res                 = $res && $coinsLogModel->create($log);
					$log ['type']        = 'summary';
					$log ['balance']     = $coinsModel->get_field($summaryExist, 'balance');
					$res                 = $res && $coinsLogModel->create($log);
					if (!$res) {
						rollback_tran();
					} else {
						commit_tran();
					}
				}
			}
			if ($res) {
				return \NuiAjaxView::refresh('添加成功！');
			} else {
				return \NuiAjaxView::error('添加成功！');
			}
		}
		$data['mid'] = $mid;

		return view('coins/addmoney.tpl', $data);
	}

	public function del($id = 0) {
		$id = intval($id);
		if (empty($id)) {
			return \NuiAjaxView::error('参数错误！');
		}
		$type = new \coins\models\MemberCoinsTypeModel();
		$res  = $type->get_one(['id' => $id, 'deleted' => 0]);

		if (empty ($res)) {
			return \NuiAjaxView::error('不存在该数据！');
		}
		if ($res ['deleted'] != 0) {
			return \NuiAjaxView::error('废弃状态 数据无法操作！');
		}

		//$ret = dbupdate('member_coins_type')->set(['deleted'=>1])->where(['id'=>$id])->exec();
		$ret = $type->update(['deleted' => 1], ['id' => $id]);

		if ($ret) {
			return \NuiAjaxView::refresh('修改成功！');
		} else {
			return \NuiAjaxView::error('删除失败！');
		}
	}

	public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {

		$name = rqst('uname');
		$uid  = rqst('uid');
		$type = rqst('type');

		if ($uid) {
			$where ['mid '] = $uid;
		} elseif ($name) {
			$where ['mname like'] = '%' . $name . '%';
		}

		if ($type) {
			$where ['type'] = $type;
		}

		$where['_cp'] = $_cp;
		$where['_lt'] = $_lt;
		$where['_sf'] = $_sf;
		$where['_od'] = $_od;
		$where['_ct'] = $_ct;

		//		$type = new  \coins\models\MemberCoinsTypeModel();

		$ac_mod  = new \coins\models\MemberCoinsAccountModel();
		$account = $ac_mod->get_page_data($where);

		$data ['total'] = $account['total'];
		$data ['rows']  = $account['rows'];
		//类型
		$data['types'] = $this->_types2arr();

		return view('coins/index_data.tpl', $data);
	}

	public function record($mid = 0, $ctype = '') {
		$type              = new  \coins\models\MemberCoinsTypeModel();
		$data              = [];
		$data['mid']       = intval($mid);
		$data['pageTitle'] = '金币详情';
		$data['type']      = trim($ctype);

		$data['types'] = $type->get_all();

		return view('coins/record.tpl', $data);
	}

	/**
	 * @param int    $_cp
	 * @param int    $_lt
	 * @param string $_sf
	 * @param string $_od
	 * @param int    $_ct
	 *
	 * @return  mixed view
	 */
	public function re_data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {
		$mid   = rqst('mid');
		$type  = rqst('type');
		$data  = [];
		$where = [];
		if (!empty($mid)) {
			$where['mid'] = $mid;
		}
		if (!empty($type)) {
			$where['type'] = $type;
		}

		$where['_cp'] = $_cp;
		$where['_lt'] = $_lt;
		$where['_sf'] = $_sf;
		$where['_od'] = $_od;
		$where['_ct'] = $_ct;

		$re_mod = new \coins\models\MemberCoinsRecordModel();
		$record = $re_mod->get_page_data($where);

		$data['total'] = $record['total'];
		$data['rows']  = $record['rows'];
		$data['types'] = $this->_types2arr();

		return view('coins/record_data.tpl', $data);
	}

	/**
	 * @param array $cond
	 * ['veiw','back']
	 *
	 * @return array
	 */
	private function _types2arr($cond = []) {
		//类型
		$p_type = new \coins\models\MemberCoinsTypeModel();
		$types  = $p_type->get_all();
		$ret    = [];
		$filter = count($cond) ? true : false;
		foreach ($types as $row) {
			if ($filter) {
				if (in_array($row['type'], $cond)) {
					$ret[ $row['type'] ] = $row['name'];
				} else {
					continue;
				}
			} else {
				$ret[ $row['type'] ] = $row['name'];
			}
		}

		return $ret;
	}
}