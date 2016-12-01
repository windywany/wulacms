<?php

class RestRemotePreferenceForm extends AbstractForm {
	private $apps = array('label' => '接入配置', 'note' => '一行一个APP配置，格式：appname,url,appid,appsecret', 'row' => 10, 'widget' => 'textarea');
}