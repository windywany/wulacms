<?php

namespace bbs\form;

use bbs\model\BbsForumsModel;

class BbsForumForm extends \db\model\ModelForm {

	private $id = array ('widget' => 'hidden','rules' => [ 'regexp(/^(0|[1-9]\d*)$/)' => '请正确填写编号' ] );
	private $upid = [ 'label' => '上级版块','group' => '1_1','col' => 6,'widget' => 'treeview','placeholder' => '顶级版块','defaults' => '{"table":"bbs_forums"}','rules' => [ 'regexp(/^(0|[1-9]\d*)$/)' => '请正确填写编号' ] ];
	private $name = [ 'label' => '版块名称','group' => '1_1','col' => 6,'rules' => [ 'required' => '请填写名称' ] ];
	private $refid = [ 'label' => '引用标识','group' => '1','col' => 3,'rules' => [ 'required' => '请填写引用标识','callback(@checkRefId,id)'=>'标识已经存在' ] ];
	private $tag = [ 'label' => '标签','group' => '1','col' => 3 ];
	private $slug = [ 'label' => '短URL','group' => '1','col' => 6,'note' => '默认为版块名称全拼','rules' => [ 'maxlength(64)' => '最大长度64个字符','callback(@checkSlug,id)' => '短URL已经存' ] ];
	private $tpl = [ 'label' => '版块模板','group' => '2','col' => '6','widget' => 'tpl','default' => 'forum.tpl' ];
	private $thread_tpl = [ 'label' => '帖子模板','group' => '2','col' => 6,'widget' => 'tpl','default' => 'forum_thread.tpl' ];
	private $_sp0  = [];
	private $rank_id = ['label'=>'最低会员等级','placeholder'=>'请选择会员等级','group' => '3','col' => 6,'widget'=>'auto','defaults'=>'member_rank,id,name,r:bbs/forum'];
	private $cost = [ 'label' => '发帖花费','group' => '3','col' => 3,'default' => 0,'rules' => [ 'regexp(/^(0|[1-9]\d*)$/)' => '请正确填写数值' ] ];
	private $reward = [ 'label' => '回帖奖励','group' => '3','col' => 3,'default' => 0,'rules' => [ 'regexp(/^(0|[1-9]\d*)$/)' => '请正确填写数值' ] ];
	private $master1  = ['label'=>'版主','placeholder'=>'请选择版主','group' => '4','col' => 6,'widget'=>'auto','defaults'=>'member,mid,nickname,r:bbs/forum'];
	private $master2  = ['label'=>'副版主','placeholder'=>'请选择副版主','group' => '4','col' => 3,'widget'=>'auto','defaults'=>'member,mid,nickname,r:bbs/forum'];
	private $master3  = ['label'=>'副版主','placeholder'=>'请选择副版主','group' => '4','col' => 3,'widget'=>'auto','defaults'=>'member,mid,nickname,r:bbs/forum'];
	private $allows = [ 'label' => '','widget' => 'checkbox','defaults' => [ 'allow_markdown' => '允许MD代码','allow_q' => '允许问题帖','allow_v' => '允许投票帖','allow_n' => '允许普通帖','allow_anonymous' => '允许匿名' ] ];
	private $sort = [ 'label' => '显示排序','group' => '5','col' => 3,'default' => 9999,'rules' => [ 'range(0,9999)' => '取值0~9999' ] ];
	private $_sp  = [];
	private $title  = ['label'=>'页面标题'];
	private $keywords = ['label'=>'关键词','widget'=>'textarea'];
	private $description = ['label'=>'页面描述','widget'=>'textarea'];
	public function checkSlug($v, $data, $msg) {
		$where['slug'] = $v;
		if(isset($data['id']) && $data['id']){
			$where['id <>'] = $data['id'];
		}
		if(dbselect()->from('{bbs_forums}')->where($where)->count('id')>0){
			return $msg;
		}
		return true;
	}
	public function checkRefId($v,$data,$msg){
		$where['refid'] = $v;
		if(isset($data['id']) && $data['id']){
			$where['id <>'] = $data['id'];
		}
		if(dbselect()->from('{bbs_forums}')->where($where)->count('id')>0){
			return $msg;
		}
		return true;
	}
	protected function init_form_fields($data, $value_set) {
		if($data['id']){
			$this->getField('upid')->setOptions([
				'defaults'=>'{"table":"bbs_forums","cid":"'.$data['id'].'"}'
			]);
		}
		$this->getField('_sp0')->setOptions(self::seperator('版块设置'));
		$this->getField('_sp')->setOptions(self::seperator('SEO设置'));
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