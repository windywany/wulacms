<?php
namespace passport\controllers;

use passport\classes\ConnectSettingForm;

class PreferenceController extends \DefaultPreferencePage {
	protected $checkUser = true;
	protected $acls      = array('index' => 'pst:system/preference', 'index_post' => 'pst:system/preference');

	protected function getCurrentURL() {
		return tourl('passport/preference', false);
	}

	protected function getForm($type, $data = array()) {
		if ($type == 'base') {
			return new \PassportPreferenceForm ();
		}
		$groups = $this->getGroups();

		if (isset ($groups [ $type ]) && !empty ($groups [ $type ])) {
			$group = $groups [ $type ];
			if ($group instanceof \PreferenceConfig) {
				return $group->getForm();
			} else {
				$cls = ucfirst($type) . 'PreferenceForm';

				return new $cls ();
			}
		}

		return new \PassportPreferenceForm ();
	}

	protected function getPreferenceGroup($type) {
		if ($type == 'base') {
			return 'passport';
		}
		$groups = $this->getGroups();
		if (isset ($groups [ $type ]) && !empty ($groups [ $type ])) {
			$group = $groups [ $type ];
			if ($group instanceof \PreferenceConfig) {
				return $group->getPreferenceGroup();
			}

			return $type;
		}

		return 'passport';
	}

	protected function getTitle() {
		return '通行证设置';
	}

	protected function getGroups() {
		$cnt    = new \PreferenceConfig('接入设置', 'passport', new ConnectSettingForm(), '');
		$groups = array('base' => '基本设置', '_cnt' => $cnt);
		$groups = apply_filter('get_passport_setting_groups', $groups);

		return $groups;
	}
}