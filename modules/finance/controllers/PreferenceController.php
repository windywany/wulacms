<?php
namespace finance\controllers;

use finance\classes\PayChannelManager;
use finance\forms\PayChannelForm;

class PreferenceController extends \DefaultPreferencePage {

	private $data;

	protected function getForm($type, $data = []) {
		$this->data = $data;
		$groups     = $this->getGroups();
		if (isset ($groups [ $type ])) {
			return $groups [ $type ]->getForm();
		}
		\Response::respond(403);
	}

	protected function getCurrentURL() {
		return tourl('finance/preference');
	}

	protected function getPreferenceGroup($type) {
		$groups = $this->getGroups();
		if (isset ($groups [ $type ])) {
			return $groups [ $type ]->getPreferenceGroup();
		} else {
			return 'finance';
		}
	}

	/*
	 * (non-PHPdoc) @see DefaultPreferencePage::getTitle()
	 */
	protected function getTitle() {
		return '支付通道配置';
	}

	protected function icando($user) {
		return icando('finance:system/preference', $user);
	}

	protected function getGroups() {
		$groups   = [];
		$channels = PayChannelManager::getChannels();
		if (!empty ($channels)) {
			foreach ($channels as $channel => $obj) {
				$form = new PayChannelForm ($this->data);
				$obj->getSettingForm($form);
				$groups [ $channel ] = new \PreferenceConfig ($obj->getName(), 'gateway_' . $channel, $form);
			}
		} else {
			$groups ['base'] = new \PreferenceConfig ('特别说明', 'finance', new PayChannelForm ($this->data));
		}

		return $groups;
	}
}