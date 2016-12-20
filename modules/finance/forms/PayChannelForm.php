<?php
namespace finance\forms;
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/9/5
 * Time: 18:44
 */
class PayChannelForm extends \AbstractForm {
	private $note = array('label' => '说明', 'widget' => 'textarea', 'group' => '9', 'col' => 10, 'rules' => []);

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
}