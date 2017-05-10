<?php

class AppAdsForm extends AbstractForm {
	private $id = array ('widget' => 'hidden','group'=>1,'rules' => array ('digits' => '编号只能是数字.' ,'regexp(/^[1-9][\d]*$/)' => '编号只能是数字,最大长度为10') );
	private $name = array ('label' => '配置名称','group'=>1,'col'=>6,'rules' => array ('required' => '请填写配置名称' ) );
	private $os = array ('label' => '操作系统','group'=>1,'col'=>6,'widget' => 'radio','rules' => array ('required' => '请选择操作系统.' ) ,'default' => '1','defaults' => "1=Android\n2=iOS");
	private $banner = array ('label' => '横幅广告','group'=>2,'col'=>3,'rules' => array ('required' => '请填写横幅广告.' ),'note'=>'形如： 23232:baidu' );
	private $bottom = array ('label' => '底部广告','group'=>2,'col'=>3,'note'=>'形如： 23232:baidu' );
	private $screen = array ('label' => '插屏广告','group'=>2,'col'=>3,'rules' => array ('required' => '请填写插屏广告.' ),'note'=>'形如： 23232:baidu' );
	private $stream = array ('label' => '信息流广告','group'=>2,'col'=>3,'rules' => array ('required' => '请填写信息流广告.' ) ,'note'=>'形如： 23232:baidu');
	private $clickinsert = array ('label' => '点击插屏广告','group'=>3,'col'=>6,'rules' => array ('required' => '请填写点击插屏广告.' ) ,'note'=>'形如： 23232:baidu');
	private $probability = array ('label' => '点击插屏广告概率','group'=>3,'col'=>6,'rules' => array ('required' => '请填写点击插屏广告概率.' ),'default'=>'50');
}
