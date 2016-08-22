<?php
class BaiduKitPreferenceForm extends AbstractForm {
	private $enable_bd = array ('label' => '启用百度工具','group' => 1,'col' => 3,'widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $enable_mt = array ('label' => '启用PC页转手机页','group' => 1,'col' => 3,'widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $bd_rest_url = array ('label' => '主动推送地址','group' => 2,'col' => 9,'rules' => array ('required(enable_bd_1:checked)' => '请填写地址' ) );
	private $push_interval = array ('label' => '推送间隔','group' => 2,'col' => 3,'widget' => 'select','default' => '24','defaults' => "0=不推送\n-1=实时\n1=1小时\n2=2小时\n3=3小时\n6=6小时\n8=8小时\n24=24小时" );
}