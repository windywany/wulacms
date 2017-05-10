<?php
class AdsPreferencesForm extends AbstractForm {
	private $ads = array ('label' => '开关','widget' => 'radio','default' => 0,'defaults' => [ 0 => '关闭',1 => '开启' ],'rules' => array ('required' => '请选择' ) );
}