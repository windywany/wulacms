<?php
class CommentMsgVipPostForm extends AbstractForm {
	private $author = array ('label' => '用户','rules' => array ('required' => '用户不能为空' ) );
	private $author_email = array ('label' => '电子邮件','rules' => array ('email' => '请填写合法的邮箱地址' ) );
	private $author_url = array ('label' => '主页URL','rules' => array ('url' => '请填写合法的网站URL' ) );
	private $author_phone = array ('label' => '联系电话' );
	private $author_weibo = array ('label' => '新浪微博' );
	private $author_qq = array ('label' => '腾讯QQ' );
	private $author_weixin = array ('label' => '腾讯微博' );
	private $author_address = array ('label' => '联系地址' );
	private $content = array ('rules' => array ('required' => '请填写内容' ) );
	private $subject;
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
}