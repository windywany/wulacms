<?php

namespace passport\classes;

class ConnectSettingForm extends \AbstractForm {
	private $connect_to   = array('group' => '0_1', 'col' => '3', 'label' => '接入通行证服务器', 'widget' => 'radio', 'default' => '0', 'defaults' => "0=否\n1=是");
	private $sync_member  = array('group' => '0_1', 'col' => '3', 'label' => '同步会员数据', 'widget' => 'radio', 'default' => '0', 'defaults' => "0=否\n1=是");
	private $passport_url = array('group' => '0_2', 'col' => '6', 'label' => '接入通行证的接口地址', 'rules' => array('required(connect_to_1:checked:1)' => '请填写', 'url' => '请填写正确的URL。'));
	private $appid        = array('group' => '0_2', 'col' => '3', 'label' => '应用ID', 'note' => '本网站在通行证服务器的注册的应用的应用ID', 'rules' => array('required(connect_to_1:checked:1)' => '请填写'));
	private $appsecret    = array('group' => '0_2', 'col' => '3', 'label' => '应用安全码', 'note' => '本网站在通行证服务器的注册的应用的应用安全码.', 'rules' => array('required(connect_to_1:checked:1)' => '请填写'));
}