<?php

class PreferenceController extends DefaultPreferencePage {
	protected function getPreferenceGroup($type) {
		return 'mobiapp';
	}

	protected function getCurrentURL() {
		return tourl('mobiapp/preference',false);
	}

	protected function getForm($type = '', $data = array()) {
		if ($type == 'base') {
			return new MobiAppPreferencesForm ();
		} else if ($type == 'upyuncdn') {
			return new UpYunPreferencesForm();
		} else if ($type == 'xinge') {
			return new XingePreferencesForm();
		} else if ($type == 'ads') {
			return new AdsPreferencesForm();
		}

		return new MobiAppPreferencesForm ();
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see DefaultPreferencePage::icando()
	 */
	protected function icando($user) {
		return icando('mobiapp:system/preference');
	}

	protected function getGroups() {
		return array('base' => 'APP配置', 'upyuncdn' => 'UPYUN CDN配置', 'xinge' => '信鸽推送', 'ads' => '广告配置');
	}

	protected function getTitle() {
		return '移动端设置';
	}
}