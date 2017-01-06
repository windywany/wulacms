<?php

namespace coins\forms;

class ConfigForm extends \AbstractForm {
	protected $__cfg_group  = 'coins_basic';
	private   $coins_name   = array('group' => '2', 'col' => '3', 'label' => '金币名称', 'default' => '金币', 'rules' => ['required' => '请填写单名称']);
	private   $coins_unit   = array('group' => '2', 'col' => '3', 'label' => '单位', 'default' => '个', 'rules' => ['required' => '请填写单位']);
	private   $rmb_to_coins = array('group' => '2', 'col' => '3', 'label' => '兑换比例', 'default' => '10', 'rules' => ['required' => '请填写兑换比例', 'range(1,10000)' => '介于1与10000之间'], 'note' => '1元钱可兑换金币数量');
}