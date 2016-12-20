<?php
namespace points\forms;
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/9/5
 * Time: 18:44
 */
class PointsTypeForm extends \AbstractForm {
	private $id           = array('widget' => 'hidden');
	private $name         = array('label' => '积分名称', 'group' => '1', 'col' => 3, 'rules' => ['required' => 'Required']);
	private $type         = array('label' => '积分种类', 'group' => '1', 'col' => 3, 'rules' => ['required' => 'Required', 'callback(@checkType,id)' => '已存在']);
	private $use_priority = array('label' => '使用优先', 'group' => '2', 'col' => 3, 'rules' => ['required' => 'Required'], 'note' => '值越大越优先使用');
	private $note         = array('label' => '备注', 'widget' => 'textarea', 'rows' => 10, 'group' => 3, 'col' => 6);

	public function checkType($value, $data, $msg) {
		$rst            = dbselect('id')->from('{member_points_type}');
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
	/*
	protected function init_form_fields($data, $value_set)
	{
		$a[0] = 'AAA';
		$a[1] = 'BBB';
		$a[2] = 'CCCC';
		$a[3] = 'DDD';
		$this->getField('selex')->setOptions(['defaults'=>$a]);
		$this->addField('_aaa',self::seperator('以下配置XXX'));
		$this->addField('ss',['label'=>'adfasdf','group'=>'1_2','col'=>6,'widget'=>'date','default'=>date("Y-m-d"),'tab_acc'=>'tab:x','defaults'=>'{"to":"se"}']);
		$this->addField('se',['label'=>'adfasdf','group'=>'1_2','col'=>6,'widget'=>'date','default'=>date("Y-m-d"),'tab_acc'=>'tab:x','defaults'=>'{"from":"ss"}']);
	}
	*/
}