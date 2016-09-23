<?php
/**
 *
 * User: Leo Ning.
 * Date: 9/12/16 22:50
 */

namespace bbs\controllers;

use bbs\model\BbsForumsModel;

/**
 * Class ThreadController
 * @package bbs\controllers
 * @checkUser
 */
class ThreadController extends \Controller {
	protected $acls = ['*' => 'r:bbs/thread'];

	/**
	 *
	 * @return \View 视图.
	 */
	public function index() {
		$data ['canDel'] = icando('d:bbs/thread');
		$model           = new BbsForumsModel ();
		$data ['items']  = $model->getTreeData(0, 1000);
		$data ['search'] = false;

		return view('thread/index.tpl', $data);
	}

	public function data() {
		$data = [];

		return view('thread/data.tpl', $data);
	}

	public function qa() {
		$data ['canDel'] = icando('d:bbs/thread');
		$model           = new BbsForumsModel ();
		$data ['items']  = $model->getTreeData(0, 1000);

		return view('thread/index.tpl', $data);
	}

	public function qa_data() {

	}

	public function vote() {
		$data ['canDel'] = icando('d:bbs/thread');
		$model           = new BbsForumsModel ();
		$data ['items']  = $model->getTreeData(0, 1000);

		return view('thread/index.tpl', $data);
	}

	public function vote_data() {

	}
}