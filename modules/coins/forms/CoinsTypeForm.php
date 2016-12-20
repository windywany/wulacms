<?php

namespace coins\forms;

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/9/5
 * Time: 18:44
 */
class CoinsTypeForm extends \AbstractForm {
	private $id           = array('widget' => 'hidden');
	private $name         = array('label' => '金币名称', 'group' => '1', 'col' => 3, 'rules' => ['required' => 'Required']);
	private $type         = array('label' => '金币种类', 'group' => '1', 'col' => 3, 'rules' => ['required' => 'Required', 'callback(@checkType,id)' => '已存在']);
	private $can_withdraw = ['label' => '是否可提现', 'widget' => 'radio', 'default' => 0, 'defaults' => "0=否\n1=是"];
	private $use_priority = array('label' => '使用优先', 'group' => '2', 'col' => 3, 'rules' => ['required' => 'Required'], 'note' => '值越大越优先使用');
	private $note         = array('label' => '备注', 'widget' => 'textarea', 'rows' => 10, 'group' => '3', 'col' => 6);

	public function checkType($value, $data, $msg) {
		$rst            = dbselect('id')->from('{member_coins_type}');
		$where ['type'] = $value;
		if (!empty ($data ['id'])) {
			$where ['id !='] = $data ['id'];
		}
		$rst->where($where);
		if ($rst->count('id') > 0) {
			return $msg;
		}

		return true;
	}
}