<?php

namespace bbs\form;

use bbs\model\BbsForumsModel;

class BbsForumForm extends \db\model\ModelForm {
	private $id = array ('widget' => 'hidden','rules' => [ 'regexp(/^(0|[1-9]\d*)$/)' => '请正确填写编号' ] );
	private $upid = [ 'label' => '上级版块','group' => '1_1','col' => 6,'widget' => 'treeview','placeholder' => '顶级版块','defaults' => '{"table":"bbs_forums"}','rules' => [ 'regexp(/^(0|[1-9]\d*)$/)' => '请正确填写编号' ] ];
	private $name = [ 'label' => '版块名称','group' => '1_1','col' => 6,'rules' => [ 'required' => '请填写名称' ] ];
	private $sort = [ 'label' => '显示排序','group' => '1','col' => 3,'default' => 9999,'rules' => [ 'range(0,9999)' => '取值0~9999' ] ];
	private $tag = [ 'label' => '标签','group' => '1','col' => 3 ];
	private $slug = [ 'label' => '短URL','group' => '1','col' => 6,'note' => '默认为版块名称全拼','rules' => [ 'maxlength(64)' => '最大长度64个字符','callback(@checkSlug,id)' => '短URL已经存' ] ];
	private $tpl = [ 'label' => '版块模板','group' => '2','col' => '6','widget' => 'tpl','default' => 'forum.tpl' ];
	private $thread_tpl = [ 'label' => '帖子模板','group' => '2','col' => 6,'widget' => 'tpl','default' => 'forum_thread.tpl' ];
	private $allows = [ 'label' => '版块配置','widget' => 'checkbox','defaults' => [ 'allow_html' => '允许HTML','allow_markdown' => '允许MD代码','allow_bbscode' => '允许BBS代码','allow_q' => '允许问题帖','allow_v' => '允许投票帖','allow_n' => '允许普通帖','allow_anonymous' => '允许匿名' ] ];
	private $cost = [ 'label' => '发帖花费','group' => '3','col' => 3,'default' => 0,'rules' => [ 'regexp(/^(0|[1-9]\d*)$/)' => '请正确填写数值' ] ];
	public function checkSlug($v, $data, $msg) {
		return true;
	}

	protected function init_form_fields($data, $value_set) {
		if($data['id']){
			$this->getField('upid')->setOptions([
				'defaults'=>'{"table":"bbs_forums","cid":"'.$data['id'].'"}'
			]);
		}
	}


	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \db\model\ModelForm::createModel()
	 */
	protected function createModel($dialect) {
		return new BbsForumsModel ( $dialect );
	}
}