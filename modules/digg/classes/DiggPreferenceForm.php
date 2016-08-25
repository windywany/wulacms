<?php
class DiggPreferenceForm extends AbstractForm {	
	private $digg0_enabled = array ('group' => '1','col' => 3,'label' => '启用digg_0','widget' => 'radio','defaults' => "0=否\n1=是",'default' => '0' );
	private $digg0_name = array ('group' => '1','col' => 3,'label' => '名称','default' => '顶' );
	private $digg1_enabled = array ('group' => '1','col' => 3,'label' => '启用digg_1','widget' => 'radio','defaults' => "0=否\n1=是",'default' => '0' );
	private $digg1_name = array ('group' => '1','col' => 3,'label' => '名称','default' => '踩' );
	private $digg2_enabled = array ('group' => '3','col' => 3,'label' => '启用digg_2','widget' => 'radio','defaults' => "0=否\n1=是",'default' => '0' );
	private $digg2_name = array ('group' => '3','col' => 3,'label' => '名称' );
	private $digg3_enabled = array ('group' => '3','col' => 3,'label' => '启用digg_3','widget' => 'radio','defaults' => "0=否\n1=是",'default' => '0' );
	private $digg3_name = array ('group' => '3','col' => 3,'label' => '名称' );
	private $digg4_enabled = array ('group' => '5','col' => 3,'label' => '启用digg_4','widget' => 'radio','defaults' => "0=否\n1=是",'default' => '0' );
	private $digg4_name = array ('group' => '5','col' => 3,'label' => '名称' );
	private $digg5_enabled = array ('group' => '5','col' => 3,'label' => '启用digg_5','widget' => 'radio','defaults' => "0=否\n1=是",'default' => '0' );
	private $digg5_name = array ('group' => '5','col' => 3,'label' => '名称' );
	private $digg6_enabled = array ('group' => '7','col' => 3,'label' => '启用digg_6','widget' => 'radio','defaults' => "0=否\n1=是",'default' => '0' );
	private $digg6_name = array ('group' => '7','col' => 3,'label' => '名称' );
	private $digg7_enabled = array ('group' => '7','col' => 3,'label' => '启用digg_7','widget' => 'radio','defaults' => "0=否\n1=是",'default' => '0' );
	private $digg7_name = array ('group' => '7','col' => 3,'label' => '名称' );
	private $digg8_enabled = array ('group' => '9','col' => 3,'label' => '启用digg_8','widget' => 'radio','defaults' => "0=否\n1=是",'default' => '0' );
	private $digg8_name = array ('group' => '9','col' => 3,'label' => '名称' );
	private $digg9_enabled = array ('group' => '9','col' => 3,'label' => '启用digg_9','widget' => 'radio','defaults' => "0=否\n1=是",'default' => '0' );
	private $digg9_name = array ('group' => '9','col' => 3,'label' => '名称' );
	public static function getDiggFlags() {
		$values = dbselect ( 'name,value' )->from ( '{preferences}' )->where ( array ('preference_group' => 'dig' ) )->toArray ( 'value', 'name' );
		return $values;
	}
}
