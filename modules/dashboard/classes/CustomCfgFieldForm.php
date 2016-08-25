<?php
class CustomCfgFieldForm extends AbstractForm {
	private $cfg = array ('rules' => array ('required' => '配置组不能为空.' ) );
	private $name = array ('rules' => array ('required' => '字段名不能为空.','regexp(/^[a-z][a-z0-9_]*$/i)' => '字段名只能是字母,数字和下划线的组合.' ) );
	private $label = array ('rules' => array ('required' => '名称不能为空.' ) );
	private $group = array ('rules' => array ('regexp(/^[\d]+$/i)' => '只能是数字。' ) );
	private $col = array ('rules' => array ('regexp(/^([1][0-2]?|[0-9])$/i)' => '只能是大于0小于13的数字。' ) );
	private $type = array ('rules' => array ('required' => '请选择一个输入控件.' ) );
	private $sort = array ('rules' => array ('range(0,1000)' => '最小值为0，最大值999。' ) );
	private $defaults;
}