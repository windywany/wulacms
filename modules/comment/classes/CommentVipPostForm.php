<?php
class CommentVipPostForm extends AbstractForm {
	private $page_id = array ('rules' => array ('required' => '页面ID不能为空','regexp(/^[1-9]\d*$/)' => '未知页面编号','callback(@checkPageId)' => '页面不存在.' ) );
	private $author = array ('rules' => array ('required' => '用户不能为空' ) );
	private $author_email = array ('rules' => array ('email' => '请填写合法的邮箱地址' ) );
	private $author_url = array ('rules' => array ('url' => '请填写合法的网站URL' ) );
	private $content = array ('rules' => array ('required' => '内容不能为空' ) );
	private $parent = array ('rules' => array ('regexp(/^[1-9]\d*$)' => '未知页面编号' ) );
	public function checkPageId($value, $data, $message) {
		return dbselect ( 'id' )->from ( '{cms_page}' )->where ( array ('id' => $value,'allow_comment' => 1 ) )->exist ( 'id' ) ? true : $message;
	}
}