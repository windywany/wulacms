<?php
namespace pay\classes;

class PayChannelForm extends \AbstractForm {
	private $channels       = array('label' => '请选择要启用的支付通道', 'widget' => 'checkbox');
	private $success_url    = ['label' => '充值成功跳转页', 'note' => '可用参数order_id', 'rules' => ['required' => '请填写', 'url' => '请正确填写URL']];
	private $failure_url    = ['label' => '充值失败跳转页', 'note' => '可用参数order_id', 'rules' => ['required' => '请填写', 'url' => '请正确填写URL']];
	private $allow_withdraw = ['label' => '允许提现', 'widget' => 'radio', 'default' => '0', 'defaults' => "0=否\n1=是"];

	protected function init_form_fields($data, $value_set) {
		$channels = PayChannelManager::getChannels();
		$chs      = [];
		foreach ($channels as $ch => $channel) {
			$chs[ $ch ] = $channel->getName();
		}
		$this->getField('channels')->setOptions(['defaults' => $chs]);
	}

	public function getChannelsValue($value) {
		if (is_array($value)) {
			return implode(',', $value);
		}

		return '';
	}
}