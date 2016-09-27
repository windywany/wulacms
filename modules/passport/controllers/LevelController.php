<?php

class LevelController extends Controller {
	protected $checkUser = true;
	private   $rankModel = null;
	protected $acls      = ['index' => 'r:account/level', 'index_post' => 'c:account/level', 'del' => 'd:account/level'];

	public function preRun($method) {
		parent::preRun($method);
		$this->rankModel = new passport\models\MemberRankModel ();
	}

	public function index($method) {
		$data ['ranks'] = $this->rankModel->get_all([]);

		return view('members/rank.tpl', $data);
	}

	public function index_post() {
		$post = $_POST;
		if (empty ($post)) {
			return NuiAjaxView::error('数据错误');
		}
		start_tran();
		foreach ($post as $k => $p) {
			$exsist = $this->rankModel->exist(['id' => $k], 'id');
			if ($exsist) {
				$p ['update_time'] = time();
				$res               = $this->rankModel->update($p, ['id' => $k]);
			} else {
				$res = $this->rankModel->create($p);
			}
			if (!$res) {
				break;
			}
		}
		if ($res) {
			commit_tran();

			return NuiAjaxView::ok('更新成功', 'click', '#refresh');
		} else {
			rollback_tran();

			return NuiAjaxView::error('更新失败');
		}
	}

	public function del($id = 0) {
		if (dbdelete()->from($this->rankModel->table)->where(['id' => $id])->exec(true)) {
			return NuiAjaxView::callback('delSuccess', ['id' => $id]);
		} else {
			return NuiAjaxView::error('删除失败');
		}
	}
}