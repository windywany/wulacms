<?php

namespace bbs\controllers;

use bbs\model\BbsForumsModel;
use bbs\form\BbsForumForm;

class ForumController extends \Controller {
	private $allows_flag = [ 'allow_html','allow_markdown','allow_bbscode','allow_q','allow_v','allow_n','allow_anonymous' ];
	protected $acls = [ '*' => 'r:bbs/forum','save' => 'id|u:bbs/forum;c:bbs/forum','add' => 'c:bbs/forum','del' => 'd:bbs/forum','csort' => 'u:cms/page' ];
	protected $checkUser = true;
	public function index() {
		$model = new BbsForumsModel ();
		$data ['items'] = $model->getTreeData ( 0, 1000 );
		$data ['cnt'] = $model->count ( [ 'deleted' => 0 ] );
		$data ['search'] = false;
		$data ['canAdd'] = icando ( 'c:bbs/forum' );
		$data ['canDel'] = icando ( 'd:bbs/forum' );
		$data ['canEdit'] = icando ( 'u:bbs/forum' );
		return view ( 'forum/index.tpl', $data );
	}
	public function data($_tid = 0) {
		$_tid = intval ( $_tid );
		$model = new BbsForumsModel ();
		$data ['items'] = $model->getTreeData ( $_tid );
		return view ( 'forum/data.tpl', $data );
	}
	public function add($upid = 0) {
		$form = new BbsForumForm ();
		$model = $form->getModel ();
		if ($upid) {
			$data = $model->get ( $upid,'tpl,thread_tpl,allow_html,allow_markdown,allow_bbscode,allow_q,allow_v,allow_n,allow_anonymous,cost' );
			$data ['upid'] = $upid;
			foreach ( $this->allows_flag as $a ) {
				if ($data [$a]) {
					$data ['allows'] [] = $a;
				}
			}
		} else {
			$data ['allows'] = [ 'allow_markdown','allow_bbscode','allow_n' ];
		}
		$data['oupid'] = $upid;
		$data ['rules'] = $form->rules ();
		$data ['formName'] = $form->getName ();
		$data ['widgets'] = new \DefaultFormRender ( $form->buildWidgets ( $data ) );
		return view ( 'forum/form.tpl', $data );
	}
	public function edit($id) {
		$id = (int)$id;
		if(empty($id)){
			\Response::respond(404);
		}
		$forum = new BbsForumsModel();
		$data = $forum->get($id);
		if(!$data){
			\Response::respond(404);
		}
		foreach ( $this->allows_flag as $a ) {
			if ($data [$a]) {
				$data ['allows'] [] = $a;
			}
		}
		$data['oupid'] = $data['upid'];
		$form =new BbsForumForm($data);
		$data['rules'] = $form->rules();
		$data['formName'] = $form->getName();
		$data['widgets'] = new \DefaultFormRender($form->buildWidgets($data));
		return view('forum/form.tpl',$data);
	}
	public function del($id) {
	}
	public function save($oupid='') {
		$form = new BbsForumForm ();
		$forum = $form->getModel ();
		$data = $form->valid ();
		if ($data) {
			$oupid = $oupid?intval($oupid):0;
			$allows = $data ['allows'] ? $data ['allows'] : [ ];
			unset ( $data ['allows'] );
			$data ['update_time'] = time ();
			$data ['update_uid'] = $this->user->getUid ();
			$data ['deleted'] = 0;
			if (empty ( $data ['slug'] )) {
				$data ['slug'] = \Pinyin::c ( $data ['name'] );
			}
			if (empty ( $data ['upid'] )) {
				$data ['upid'] = 0;
			}
			foreach ( $this->allows_flag as $a ) {
				$data [$a] = in_array ( $a, $allows );
			}
			if ($data ['id']) {
				$id = $data ['id'];
				$rst = $forum->update ( $data );
			} else {
				unset ( $data ['id'] );
				$data ['create_time'] = $data ['update_time'];
				$data ['create_uid'] = $data ['update_uid'];
				$data ['rank_id'] = 0;
				$rst = $forum->create ( $data );
				$id = $rst;
			}
			if ($rst) {
				$reloadIds = $data['upid'];
				if(!$data['upid'] || !$oupid){
					$reloadIds = 0;
				}else if($data['upid'] != $oupid){
					$reloadIds.=','.$oupid;
				}
				return \NuiAjaxView::callback ( 'reloadForumTree', [ 'id' => $id,'upid' => $reloadIds], '版块信息已保存.' );
			} else {
				$errors = $forum->getErrors ();
				if (is_array ( $errors )) {
					return \NuiAjaxView::validate ( $form->getName (), '表单数据有错', $errors );
				} else {
					return \NuiAjaxView::error ( '数据库出错:' . $errors );
				}
			}
		} else {
			return \NuiAjaxView::validate ( $form->getName (), '表单数据有错', $form->getErrors () );
		}
	}
}