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
	 * @param string $type
	 *
	 * @return \View 视图.
	 */
	public function index($type = 'n') {
		$data ['canDel'] = icando('d:bbs/thread');
		$data ['type']   = $type;
		$model            = new BbsForumsModel ();
		$data ['items']   = $model->getTreeData(0, 1000);
		$data ['search']  = false;
		return view('thread/index.tpl', $data);
	}
	public function data(){

	}
}