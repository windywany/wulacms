<?php
class CommentMsgEditForm extends AbstractForm {
	private $id = array ('widget' => 'hidden','rules' => array ('required' => '留言编号不能为空','regexp(/^[1-9][\d]*$/)' => '错误的留言编号.' ) );
	private $author = array ('label' => '用户','group' => '1','col' => 6,'rules' => array ('required' => '用户不能为空' ) );
	private $author_email = array ('label' => '电子邮件','group' => '1','col' => 6,'rules' => array ('email' => '请填写合法的邮箱地址' ) );
	private $author_url = array ('label' => '主页URL','rules' => array ('url' => '请填写合法的网站URL' ) );
	private $author_phone = array ('label' => '联系电话','group' => '2','col' => 3 );
	private $author_weibo = array ('label' => '新浪微博','group' => '2','col' => 3 );
	private $author_qq = array ('label' => '腾讯QQ','group' => '2','col' => 3 );
	private $author_weixin = array ('label' => '腾讯微博','group' => '2','col' => 3 );
	private $create_time = array ('label' => '提交于','group' => '3','col' => 6,'widget' => 'date' );
	private $create_time1 = array ('skip' => true,'label' => '&nbsp;','group' => '3','col' => 3,'widget' => 'time' );
	private $author_address = array ('label' => '联系地址','widget' => 'textarea' );
	private $comment_content = array ('norender' => true,'rules' => array ('required' => '请填写内容' ) );
	private $status = array ('norender' => true );
	private $subject = array ('norender' => true );
	public function init_form_fields($data, $value_set) {
		if (! bcfg ( 'enable_phone@comment' )) {
			unset ( $this->__form_fields ['author_phone'] );
		} else if (cfg ( 'enable_phone@comment' ) == '2') {
			$this->getField ( 'author_phone' )->addValidate ( 'required', '请填写联系电话' );
		}
		
		if (! bcfg ( 'enable_qq@comment' )) {
			unset ( $this->__form_fields ['author_qq'] );
		} else if (cfg ( 'enable_qq@comment' ) == '2') {
			$this->getField ( 'author_qq' )->addValidate ( 'required', '请填写腾讯QQ' );
		}
		
		if (! bcfg ( 'enable_weixin@comment' )) {
			unset ( $this->__form_fields ['author_weixin'] );
		} else if (cfg ( 'enable_weixin@comment' ) == '2') {
			$this->getField ( 'author_weixin' )->addValidate ( 'required', '请填写微信' );
		}
		
		if (! bcfg ( 'enable_weibo@comment' )) {
			unset ( $this->__form_fields ['author_weibo'] );
		} else if (cfg ( 'enable_weibo@comment' ) == '2') {
			$this->getField ( 'author_weibo' )->addValidate ( 'required', '请填写新浪微博' );
		}
		
		if (! bcfg ( 'enable_address@comment' )) {
			unset ( $this->__form_fields ['author_address'] );
		} else if (cfg ( 'enable_address@comment' ) == '2') {
			$this->getField ( 'author_address' )->addValidate ( 'required', '请填写联系地址' );
		}
		// $fs = CommentPreferenceForm::getCustomContactField ();
	}
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
