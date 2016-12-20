<?php
namespace points\controllers;
/**
 *后台积分类型
 * User: FLy
 * Date: 2016/6/27
 * Time: 11:59
 */
class PointsController extends \Controller {
	protected $checkUser = true;

	//申请中
	public function index() {
		$p_type            = new \points\models\MemberPointsTypeModel();
		$data              = array();
		$data['pageTitle'] = '帐户列表';
		$data['types']     = $p_type->get_all();

		return view('points/index.tpl', $data);
	}

	//后台添加积分
	public function addpoints() {

		$data = array();
		//类型分类
		$data['type'] = dbselect('*')->from('{member_points_type}')->toArray();
		if (isset($_POST) AND !empty($_POST)) {
			if (empty($_POST['userid'])) {
				return \NuiAjaxView::error('填写用户id！');
			}
			if (empty($_POST['points'])) {
				return \NuiAjaxView::error('填写用户积分！');
			}

			$_POST['points'] = (int)$_POST['points'];

			//查看是否已有积分
			$where        = array();
			$where['mid'] = $_POST['userid'];

			//查看用户是否存在
			$dbuser = dbselect('*')->from('{member}')->where($where)->get();
			if (empty($dbuser)) {
				return \NuiAjaxView::error('填写正确用户id！');
			}

			$where['type'] = $_POST['type'];
			$dbpoints      = dbselect('*')->from('{member_points_account}')->where($where)->get();
			//添加积分记录表
			$data                = array();
			$data['create_time'] = time();
			$data['mid']         = $_POST['userid'];
			$data['type']        = $_POST['type'];
			$data['balance']     = $_POST['points'];
			$data['amount']      = 1;
			$data['subject']     = '手工添加积分';
			$data['note']        = $this->user['username'] . '添加';
			dbinsert($data)->into('{member_points_record}')->exec();
			//添加或修改积分
			$data = array();
			if (empty($dbpoints)) {

				//添加
				$data['create_time'] = time();
				$data['mid']         = $_POST['userid'];
				$data['type']        = $_POST['type'];
				$data['amount']      = $_POST['points'];
				$data['balance']     = $_POST['points'];
				$data['mname']       = $dbuser['nickname'];

				dbinsert($data)->into('{member_points_account}')->exec();

			} else {

				$data['amount']  = $dbpoints['amount'] + $_POST['points'];
				$data['balance'] = $dbpoints['balance'] + $_POST['points'];
				//修改
				dbupdate('member_points_account')->set($data)->where(array('id' => $dbpoints['id']))->exec();
			}

			return \NuiAjaxView::refresh('添加成功！');
		}

		return view('points/addpoints.tpl', $data);
	}

	public function del($id = 0) {
		$id = intval($id);

		if (empty($id)) {
			\NuiAjaxView::error('参数错误！');
		}
		$res = dbselect('*')->from('{member_points_type}')->where(array('id' => $id, 'deleted' => 0))->get(0);

		if (empty ($res)) {
			return \NuiAjaxView::error('不存在该数据！');
		}
		if ($res ['deleted'] != 0) {
			return \NuiAjaxView::error('废弃状态 数据无法操作！');
		}

		$ret = dbupdate('member_points_type')->set(['deleted' => 1])->where(['id' => $id])->exec();
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

		$pa      = new \points\models\MemberPointsAccountModel();
		$account = $pa->get_page_data($where);

		$data ['total'] = $account['total'];
		$data ['rows']  = $account['rows'];
		/*类型表*/
		$data['types'] = $this->_types2arr();

		return view('points/index_data.tpl', $data);
	}

	public function record($mid = '', $type = '') {
		$p_type            = new  \points\models\MemberPointsTypeModel();
		$data              = [];
		$data['mid']       = $mid;
		$data['type']      = trim($type);
		$data['pageTitle'] = '积分详情';
		$data['types']     = $p_type->get_all();

		return view('points/record.tpl', $data);
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
		$mid   = irqst('mid');
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

		$p_re   = new \points\models\MemberPointsRecordModel();
		$record = $p_re->get_page_data($where);

		$data['total'] = $record['total'];
		$data['rows']  = $record['rows'];
		/*类型表*/
		$data['types'] = $this->_types2arr();

		return view('points/record_data.tpl', $data);
	}

	/*类型组装成数组*/
	private function _types2arr() {
		//类型
		$p_type = new \points\models\MemberPointsTypeModel();
		$types  = $p_type->get_all();
		$ret    = [];
		foreach ($types as $row) {
			$ret[ $row['type'] ] = $row['name'];
		}

		return $ret;
	}
}