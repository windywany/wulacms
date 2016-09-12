<?php

namespace bbs\controllers;

use bbs\model\BbsForumsModel;
use bbs\form\BbsForumForm;
use bbs\model\BbsThreadsModel;

class ForumController extends \Controller {
	private   $allows_flag = ['allow_markdown', 'allow_q', 'allow_v', 'allow_n', 'allow_anonymous'];
	protected $acls        = ['*' => 'r:bbs/forum', 'save' => 'id|u:bbs/forum;c:bbs/forum', 'add' => 'c:bbs/forum', 'del' => 'd:bbs/forum', 'csort' => 'u:cms/page'];
	protected $checkUser   = true;

	public function index() {
		$model            = new BbsForumsModel ();
		$data ['items']   = $model->getTreeData(0, 1000);
		$data ['cnt']     = $model->count(['deleted' => 0]);
		$data ['search']  = false;
		$data ['canAdd']  = icando('c:bbs/forum');
		$data ['canDel']  = icando('d:bbs/forum');
		$data ['canEdit'] = icando('u:bbs/forum');

		return view('forum/index.tpl', $data);
	}

	public function data($_tid = 0) {
		$_tid           = intval($_tid);
		$model          = new BbsForumsModel ();
		$data ['items'] = $model->getTreeData($_tid);

		return view('forum/data.tpl', $data);
	}

	public function add($upid = 0) {
		$model  =  new BbsForumsModel();
		$form = $model->getForm();
		if ($upid) {
			$data         = $model->get($upid, 'tpl,thread_tpl,allow_html,allow_markdown,allow_bbscode,allow_q,allow_v,allow_n,allow_anonymous,cost');
			$data['upid'] = $upid;
			foreach ($this->allows_flag as $a) {
				if ($data [ $a ]) {
					$data ['allows'] [] = $a;
				}
			}
		} else {
			$data ['allows'] = ['allow_markdown', 'allow_bbscode', 'allow_n'];
		}
		$data['oupid']     = 0;
		$data['type']       = '1';
		$data ['rules']    = $form->rules();
		$data ['formName'] = $form->getName();
		$data ['widgets']  = new \DefaultFormRender ($form->buildWidgets($data));

		return view('forum/form.tpl', $data);
	}

	public function edit($id) {
		$id = (int)$id;
		if (empty($id)) {
			\Response::respond(404);
		}
		$forum = new BbsForumsModel();
		$data  = $forum->get($id);
		if (!$data) {
			\Response::respond(404);
		}
		foreach ($this->allows_flag as $a) {
			if ($data [ $a ]) {
				$data ['allows'] [] = $a;
			}
		}
		$data['oupid']    = $data['upid'];
		$form             = new BbsForumForm($data);
		$data['rules']    = $form->rules();
		$data['formName'] = $form->getName();
		$data['widgets']  = new \DefaultFormRender($form->buildWidgets($data));

		return view('forum/form.tpl', $data);
	}

	public function del($id) {
		$id = intval($id);
		if (empty($id)) {
			\Response::respond(404);
		}
		$forum  = new BbsForumsModel();
		$thread = new BbsThreadsModel();
		if ($forum->exist(['upid' => $id])) {
			return \NuiAjaxView::error('请先删除它的子版块');
		} else if ($thread->exist(['forum_id' => $id])) {
			return \NuiAjaxView::error('请先删除它的帖子');
		} else {
			$upid = $forum->get($id, 'upid');
			if ($upid) {
				$upid = $upid['upid'];
			} else {
				$upid = 0;
			}
			$rst = $forum->delete(['id' => $id]);
			if ($rst) {
				//TODO: update its parent's sub_forums.
				return \NuiAjaxView::callback('reloadForumTree', ['id' => 0, 'upid' => $upid], '版块已经放入回收站.');
			} else {
				return \NuiAjaxView::error('无法删除版块');
			}
		}
	}

	public function save($oupid = '') {
		$forum = new BbsForumsModel();
		$form  = $forum->getForm();
		$data  = $form->valid();
		if ($data) {
			$oupid  = $oupid ? intval($oupid) : 0;
			$allows = $data ['allows'] ? $data ['allows'] : [];
			unset ($data ['allows']);
			$data ['update_time'] = time();
			$data ['update_uid']  = $this->user->getUid();
			$data ['deleted']     = 0;
			if (empty ($data ['slug'])) {
				$data ['slug'] = \Pinyin::c($data ['name']);
			}
			if(empty($data['thread_url_pattern'])){
				$data['thread_url_pattern']='{path}/thread-{tid}.html';
			}
			if(empty($data['url'])){
				$data['url'] = '{path}/index.html';
			}
			$data['url_key'] = md5($data['url']);
			if (empty ($data ['upid'])) {
				$data ['upid'] = 0;
			}
			if ($data ['upid']) {
				$path = dbselect ( 'path' )->from ( '{bbs_forums}' )->where ( array ('id' => $data['upid'] ) )->get ( 'path' );
				$data['path'] = trim($path.'/'.$data['slug'],'/');
			}else{
				$data['path'] = trim($data['slug']);
			}
			foreach ($this->allows_flag as $a) {
				$data [ $a ] = in_array($a, $allows);
			}
			$isNew                = false;
			if ($data ['id']) {
				$id  = $data ['id'];
				$rst = $forum->updateForumWithMasters($data);
			} else {
				unset ($data ['id']);
				$data ['create_time'] = $data ['update_time'];
				$data ['create_uid']  = $data ['update_uid'];
				$data ['rank_id']     = 0;
				$rst                  = $forum->create($data);
				$id                   = $rst;
				$isNew                = true;
			}
			if ($rst) {
				$reloadIds = $data['upid'];
				$url = $forum->updateForumUrl($data['url'],$id);
				if(!$url){
					$forum->delete($id);
					return \NuiAjaxView::validate($form->getName(), '版块URL重复请重新设置版块URL规则', ['url'=>'版块URL重复请']);
				}
				if($isNew){
					$forum->update(['subforums'=>$id,'id'=>$id]);
				}
				if($oupid != $data['upid']){
					$forum->updateForumRelations($oupid,$id);
				}
				if (!$data['upid']) {
					$reloadIds = 0;
				} else if ($oupid && $data['upid'] != $oupid) {
					$reloadIds .= ',' . $oupid;
				}

				return \NuiAjaxView::callback('reloadForumTree', ['id' => $id, 'upid' => $reloadIds,'url'=>$url], '版块信息已保存.');
			} else {
				$errors = $forum->getErrors();
				if (is_array($errors)) {
					return \NuiAjaxView::validate($form->getName(), '表单数据有错', $errors);
				} else {
					return \NuiAjaxView::error('数据库出错:' . $errors);
				}
			}
		} else {
			return \NuiAjaxView::validate($form->getName(), '表单数据有错', $form->getErrors());
		}
	}
}