<?php
class SystemNoticeForm extends AbstractForm {
	private $id;
	private $expire_time;
	private $title = array ('rules' => array ('required' => '请填写通知标题.','minlength' => 3 ) );
	private $message = array ('rules' => array ('required' => '请填写通知正文.','minlength' => 10 ) );
	
}