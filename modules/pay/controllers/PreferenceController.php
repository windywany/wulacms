<?php
namespace pay\controllers;

use pay\classes\PayChannelForm;
use pay\classes\PayChannelManager;

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
		return 'pay/preference';
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
		return '支付配置';
	}

	protected function icando($user) {
		return icando('pay:system/preference', $user);
	}

	protected function getGroups() {
		static $groups = false;
		if ($groups === false) {
			$groups   = ['base' => new \PreferenceConfig ('通道配置', 'finance', new PayChannelForm ())];
			$channels = PayChannelManager::getChannels();
			$cfgchs   = cfg('channels@finance');
			$cfgchs   = explode(',', $cfgchs);
			if ($cfgchs) {
				if (!empty ($channels)) {
					foreach ($channels as $channel => $obj) {
						if (in_array($channel, $cfgchs)) {
							$form = $obj->getSettingForm(null);
							if ($form) {
								$groups [ $channel ] = new \PreferenceConfig ($obj->getName(), 'gateway_' . $channel, $form);
							}
						}
					}
				}
			}
		}

		return $groups;
	}
}