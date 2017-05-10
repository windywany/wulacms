<?php
class CommentPostForm extends AbstractForm {
	private $id = array ('widget' => 'hidden','rules' => array ('required' => '评论编号不能为空','regexp(/^[1-9][\d]*$/)' => '错误的评论编号.' ) );
	private $author = array ('label' => '用户','group' => '1','col' => 6,'rules' => array ('required' => '用户不能为空' ) );
	private $author_email = array ('label' => '电子邮件','group' => '1','col' => 6,'rules' => array ('email' => '请填写合法的邮箱地址' ) );
	private $author_url = array ('label' => '主页URL','rules' => array ('url' => '请填写合法的网站URL' ) );
	private $create_time = array ('label' => '提交于','group' => '3','col' => 6,'widget' => 'date' );
	private $create_time1 = array ('skip' => true,'label' => '&nbsp;','group' => '3','col' => 3,'widget' => 'time' );
	private $comment_content = array ('norender' => true,'rules' => array ('required' => '请填写内容' ) );
	private $status = array ('norender' => true );
	public function getCreate_timeValue($value) {
		$time = rqst ( 'create_time1' );
		if (empty ( $time )) {
			$time = '00:00:00';
		} else {
			$time .= ':00';
		}
		if (! empty ( $value )) {
			return strtotime ( $value . ' ' . $time );
		}
		return time ();
	}
}