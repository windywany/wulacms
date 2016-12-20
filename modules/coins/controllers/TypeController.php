<?php
namespace coins\controllers;
/**
 *后台积分类型
 * User: FLy
 * Date: 2016/6/27
 * Time: 11:59
 */
class TypeController extends \Controller {
	protected $checkUser = true;
	protected $acls      = ['*' => 'r:coins/type', 'add' => 'c:coins/type', 'del' => 'd:coins/type', 'edit' => 'u:coins/type', 'save' => 'id|u:coins/type;c:coins/type'];

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
		$model = new \coins\models\MemberCoinsTypeModel();
		$data  = $model->get_one(['id' => $id]);
		if (!$data) {
			\Response::respond(404);
		}
		$form             = new \coins\forms\CoinsTypeForm($data);
		$data['formName'] = $form->getName();
		$data['rules']    = $form->rules();
		$data['widgets']  = new \DefaultFormRender($form->buildWidgets($data));

		return view('type/form.tpl', $data);
	}

	//已处理
	public function add() {
		$data             = array();
		$form             = new \coins\forms\CoinsTypeForm();
		$data['rules']    = $form->rules();
		$data['formName'] = $form->getName();
		$data['widgets']  = new \DefaultFormRender($form->buildWidgets());

		return view('type/form.tpl', $data);
	}

	//作废
	public function del($id = 0) {
		$id = intval($id);

		if (empty($id)) {
			\NuiAjaxView::error('参数错误！');
		}
		$model = new \coins\models\MemberCoinsTypeModel();
		$res   = $model->get_one(['id' => $id, 'deleted' => 0]);

		if (empty ($res)) {
			return \NuiAjaxView::error('不存在该数据！');
		}
		if ($res ['deleted'] != 0) {
			return \NuiAjaxView::error('废弃状态 数据无法操作！');
		}

		$model = new \coins\models\MemberCoinsTypeModel();
		$ret   = $model->update(['deleted' => 1], ['id' => $id]);

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
		$model        = new \coins\models\MemberCoinsTypeModel();
		$type         = $model->get_page_data($where);

		$data ['total']   = $type['total'];
		$data ['rows']    = $type['rows'];
		$data ['canEdit'] = icando('u:coins/type');
		$data['canDel']   = icando('d:coins/type');

		return view('type/index_data.tpl', $data);
	}

	public function save() {
		$form = new \coins\forms\CoinsTypeForm();
		$data = $form->valid();
		if ($data) {
			$model               = new \coins\models\MemberCoinsTypeModel();
			$id                  = $data['id'];
			$data['update_time'] = time();
			$data['update_uid']  = $this->user->getUid();
			$data['deleted']     = 0;
			$data['reserved']    = 0;
			if ($id) {
				$type = $model->get($id);
				//$rst = dbupdate('{member_coins_type}')->set($data)->where(['id'=>$id])->exec();
				$rst = $model->update($data);
				if (!$rst) {
					$id = 0;
				}
				if ($type['can_withdraw'] != $data['can_withdraw']) {
					dbupdate('{member_coins_account}')->set(['can_withdraw' => $data['can_withdraw']])->where(['type' => $type['type']])->exec();
				}

				if ($type['use_priority'] != $data['use_priority']) {
					dbupdate('{member_coins_account}')->set(['use_priority' => $data['use_priority']])->where(['type' => $type['type']])->exec();
				}
			} else {
				unset($data['id']);
				//$rst = dbinsert($data)->into('{member_coins_type}')->exec();
				$rst = $model->create($data, function ($data) {
					$data['create_time'] = $data['update_time'];
					$data['create_uid']  = $data['update_uid'];

					return $data;
				});
				if ($rst) {
					$id = $rst;
				}
			}
			if ($id) {
				return \NuiAjaxView::redirect('类型已经保存', '#' . tourl('coins/type', false));
			} else {
				return \NuiAjaxView::error('保存类型出错');
			}
		} else {
			return \NuiAjaxView::validate($form->getName(), '数据类型不正确!', $form->getErrors());
		}
	}
}