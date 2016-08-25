<?php
class BaiduPushForm extends AbstractForm {
	private $bd = array ('group' => 2,'col' => 4,'label' => '开始日期','widget' => 'date','note' => '不填写会从建站那天开始推送','rules' => array ('date' => '日期格式为yyyy-mm-dd' ) );
	private $sd = array ('group' => 2,'col' => 4,'label' => '结束日期','widget' => 'date','note' => '不填写会推送到今天','rules' => array ('date' => '日期格式为yyyy-mm-dd' ) );
	private $cnt = array ('group' => 2,'col' => '4','label' => '每次推送数量','rules' => array ('digits' => '只能是大于0的数' ),'note' => '默认50条' );
}
