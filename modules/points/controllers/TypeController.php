<?php
namespace points\controllers;

/**
 *后台积分类型
 * User: FLy
 * Date: 2016/6/27
 * Time: 11:59
 */
class TypeController extends \Controller {
	protected $checkUser = true;

	//申请中
	public function index() {
		$data              = array();
		$data['pageTitle'] = '类型列表';

		return view('type/index.tpl', $data);
	}

	public function edit($id = 0) {
		$id = intval($id);
		if (empty($id)) {
			\Response::respond(403);
		}
		$p_type = new \points\models\MemberPointsTypeModel();
		$data   = $p_type->get_one(['id' => $id]);
		if (!$data) {
			\Response::respond(404);
		}
		$form             = new \points\forms\PointsTypeForm($data);
		$data['formName'] = $form->getName();
		$data['rules']    = $form->rules();
		$data['widgets']  = new \DefaultFormRender($form->buildWidgets($data));

		return view('type/form.tpl', $data);
	}

	//已处理
	public function add() {
		$form             = new \points\forms\PointsTypeForm();
		$data['formName'] = $form->getName();
		$data['rules']    = $form->rules();
		$data['widgets']  = new \DefaultFormRender($form->buildWidgets($data));

		return view('type/form.tpl', $data);
	}

	//作废
	public function del($id = 0) {
		$id = intval($id);

		if (empty($id)) {
			\NuiAjaxView::error('参数错误！');
		}
		$p_type = new \points\models\MemberPointsTypeModel();
		$res    = $p_type->get_one(['id' => $id, 'deleted' => 0]);

		if (empty ($res)) {
			return \NuiAjaxView::error('不存在该数据！');
		}
		if ($res ['deleted'] != 0) {
			return \NuiAjaxView::error('废弃状态 数据无法操作！');
		}

		$ret = $p_type->update(['deleted' => 1], ['id' => $id]);

		if ($ret) {
			return \NuiAjaxView::refresh('修改成功！');
		} else {
			return \NuiAjaxView::error('删除失败！');
		}
	}

	public function data($_cp = 1, $_lt = 20, $_sf = 'id', $_od = 'd', $_ct = 0) {

		$name = rqst('pname');
		$pid  = rqst('pid');

		if ($pid) {
			$where ['id '] = $pid;
		} elseif ($name) {
			$where ['name like'] = '%' . $name . '%';
		}

		$where ['deleted'] = 0;

		$where['_cp'] = $_cp;
		$where['_lt'] = $_lt;
		$where['_sf'] = $_sf;
		$where['_od'] = $_od;
		$where['_ct'] = $_ct;

		$p_type = new \points\models\MemberPointsTypeModel();
		$type   = $p_type->get_page_data($where);

		$data ['total'] = $type['total'];
		$data ['rows']  = $type['rows'];

		return view('type/index_data.tpl', $data);
	}

	/*
	 * 处理微信申请
	 */
	public function save() {
		$id           = intval(rqst('id'));
		$name         = rqst('name');
		$type         = rqst('type');
		$note         = rqst('note');
		$use_priority = rqst('use_priority');
		$up           = false;
		if ($id) {
			$up = true;
		}

		if (empty ($type) || empty($name)) {
			return \NuiAjaxView::error('参数错误！');
		}
		$p_type = new \points\models\MemberPointsTypeModel();
		if ($up) {
			$res = $p_type->get_one(['id' => $id, 'deleted' => 0]);
			if (empty ($res)) {
				return \NuiAjaxView::error('不存在该数据！');
			}
			if ($res ['deleted'] != 0) {
				return \NuiAjaxView::error('废弃状态 数据无法操作！');
			}
		}
		if ($up) {
			$ret = $p_type->update(['name' => $name, 'use_priority' => $use_priority, 'type' => $type, 'note' => $note], ['id' => $id]);
			if ($res['use_priority'] != $use_priority) {
				dbupdate('{member_points_account}')->set(['use_priority' => $use_priority])->where(['type' => $res['type']])->exec();
			}
		} else {
			$uid         = $this->user->getUID();
			$time        = time();
			$insert_data = ['name' => $name, 'type' => $type, 'use_priority' => $use_priority, 'note' => $note, 'create_time' => $time, 'update_time' => $time, 'create_uid' => $uid, 'update_uid' => $uid, 'reserved' => 0];
			$ret         = $p_type->create($insert_data);
		}
		if ($ret) {
			return \NuiAjaxView::redirect('修改成功！', '#' . tourl('points/type', false));
		} else {
			return \NuiAjaxView::error('信息修改错误！');
		}
	}
}